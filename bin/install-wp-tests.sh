#!/usr/bin/env bash

# Load configuration from tests/config.php if it exists
if [ -f tests/config.php ]; then
    echo "Loading configuration from tests/config.php..."
    DB_NAME=$(php -r "echo (require 'tests/config.php')['DB_NAME'];")
    DB_USER=$(php -r "echo (require 'tests/config.php')['DB_USER'];")
    DB_HOST=$(php -r "echo (require 'tests/config.php')['DB_HOST'];")
    WP_TESTS_DIR=$(php -r "echo (require 'tests/config.php')['WP_TESTS_DIR'];")
    WP_CORE_DIR=$(php -r "echo (require 'tests/config.php')['WP_CORE_DIR'];")
fi

# Use config file values or command line arguments (command line takes precedence)
DB_NAME=${1:-${DB_NAME:-wordpress_test}}
DB_USER=${2:-${DB_USER:-root}}
DB_HOST=${3:-${DB_HOST:-localhost}}
WP_VERSION=${4:-trunk}
SKIP_DB_CREATE=${5:-false}

TMPDIR=${TMPDIR-/tmp}
TMPDIR=$(echo $TMPDIR | sed -e "s/\/$//")
WP_TESTS_DIR=${WP_TESTS_DIR:-$TMPDIR/wordpress-tests-lib}
WP_CORE_DIR=${WP_CORE_DIR:-$TMPDIR/wordpress/}

echo "======================================"
echo "WordPress Test Suite Installation"
echo "======================================"
echo "Database Name: $DB_NAME"
echo "Database User: $DB_USER"
echo "Database Host: $DB_HOST"
echo "WordPress Version: $WP_VERSION"
echo "Test Directory: $WP_TESTS_DIR"
echo "WordPress Core Directory: $WP_CORE_DIR"
echo "======================================"
echo ""

# Prompt for password securely (only if we need to create the database)
if [ ${SKIP_DB_CREATE} != "true" ]; then
    # Check if password is provided via environment variable (for CI/CD)
    if [ -z "$WP_TEST_DB_PASSWORD" ]; then
        echo "MySQL password required for database creation."
        echo -n "Enter MySQL password for user '$DB_USER' (or press Enter if none): "
        read -s DB_PASS
        echo ""
    else
        DB_PASS="$WP_TEST_DB_PASSWORD"
        echo "Using password from WP_TEST_DB_PASSWORD environment variable"
    fi
    echo ""
fi

download() {
    if [ `which curl` ]; then
        curl -s "$1" > "$2";
    elif [ `which wget` ]; then
        wget -nv -O "$2" "$1"
    fi
}

echo "Using WordPress version: $WP_VERSION"

# Determine the correct SVN tag
if [[ $WP_VERSION == 'latest' ]]; then
    echo "  Detecting latest WordPress version..."
    WP_VERSION=$(curl -s 'https://api.wordpress.org/core/version-check/1.7/' | grep -o '"version":"[^"]*' | head -1 | cut -d'"' -f4)
    echo "  Latest version is: $WP_VERSION"
fi

if [[ $WP_VERSION =~ ^[0-9]+\.[0-9]+$ ]]; then
    WP_TESTS_TAG="branches/$WP_VERSION"
elif [[ $WP_VERSION =~ [0-9]+\.[0-9]+\.[0-9]+ ]]; then
    if [[ $WP_VERSION =~ [0-9]+\.[0-9]+\.[0] ]]; then
        WP_TESTS_TAG="tags/${WP_VERSION%??}"
    else
        WP_TESTS_TAG="tags/$WP_VERSION"
    fi
elif [[ $WP_VERSION == 'nightly' || $WP_VERSION == 'trunk' ]]; then
    WP_TESTS_TAG="trunk"
else
    WP_TESTS_TAG="trunk"
fi

echo "Using WordPress tests tag: $WP_TESTS_TAG"
echo ""

set -e

install_wp() {
    if [ -f "$WP_CORE_DIR/wp-load.php" ]; then
        echo "✓ WordPress core already installed at $WP_CORE_DIR"
        echo "  Skipping WordPress installation..."
        echo ""
        return;
    fi

    if [ -d $WP_CORE_DIR ]; then
        echo "  Removing empty WordPress directory..."
        rm -rf $WP_CORE_DIR
    fi

    echo "Installing WordPress core..."
    mkdir -p $WP_CORE_DIR

    if [[ $WP_VERSION == 'nightly' || $WP_VERSION == 'trunk' ]]; then
        echo "  Downloading WordPress nightly build..."
        mkdir -p $TMPDIR/wordpress-nightly
        download https://wordpress.org/nightly-builds/wordpress-latest.zip  $TMPDIR/wordpress-nightly/wordpress-nightly.zip
        echo "  Extracting WordPress nightly..."
        unzip -q $TMPDIR/wordpress-nightly/wordpress-nightly.zip -d $TMPDIR/wordpress-nightly/
        mv $TMPDIR/wordpress-nightly/wordpress/* $WP_CORE_DIR
    else
        if [ $WP_VERSION == 'latest' ]; then
            local ARCHIVE_NAME='latest'
            echo "  Downloading latest WordPress version..."
        elif [[ $WP_VERSION =~ [0-9]+\.[0-9]+ ]]; then
            local ARCHIVE_NAME="wordpress-$WP_VERSION"
            echo "  Downloading WordPress version $WP_VERSION..."
        else
            local ARCHIVE_NAME="wordpress-${WP_VERSION%??}"
            echo "  Downloading WordPress version ${WP_VERSION%??}..."
        fi
        download https://wordpress.org/${ARCHIVE_NAME}.tar.gz  $TMPDIR/wordpress.tar.gz
        echo "  Extracting WordPress..."
        tar --strip-components=1 -zxmf $TMPDIR/wordpress.tar.gz -C $WP_CORE_DIR
    fi

    echo "  Downloading database drop-in..."
    download https://raw.githubusercontent.com/markoheijnen/wp-mysqli/master/db.php $WP_CORE_DIR/wp-content/db.php
    echo "✓ WordPress core installed successfully!"
    echo ""
}

install_test_suite() {
    if [ -f "$WP_TESTS_DIR/includes/functions.php" ]; then
        echo "✓ Test suite already installed at $WP_TESTS_DIR"
        echo "  Skipping test suite installation..."
        echo ""
        return;
    fi

    if [ -d $WP_TESTS_DIR ]; then
        echo "  Removing empty test directory..."
        rm -rf $WP_TESTS_DIR
    fi

    echo "Installing WordPress test suite..."
    mkdir -p $WP_TESTS_DIR
    
    echo "  Downloading test suite includes via SVN..."
    svn co --quiet https://develop.svn.wordpress.org/${WP_TESTS_TAG}/tests/phpunit/includes/ $WP_TESTS_DIR/includes
    
    echo "  Downloading test suite data via SVN..."
    svn co --quiet https://develop.svn.wordpress.org/${WP_TESTS_TAG}/tests/phpunit/data/ $WP_TESTS_DIR/data

    if [ ! -f "$WP_TESTS_DIR/wp-tests-config.php" ]; then
        echo "  Downloading test configuration file..."
        download https://develop.svn.wordpress.org/${WP_TESTS_TAG}/wp-tests-config-sample.php "$WP_TESTS_DIR"/wp-tests-config.php
        
        echo "  Configuring test suite..."
        WP_CORE_DIR=$(echo $WP_CORE_DIR | sed 's:/\+$::')
        sed -i "s:dirname( __FILE__ ) . '/src/':'$WP_CORE_DIR/':" "$WP_TESTS_DIR"/wp-tests-config.php
        sed -i "s/youremptytestdbnamehere/$DB_NAME/" "$WP_TESTS_DIR"/wp-tests-config.php
        sed -i "s/yourusernamehere/$DB_USER/" "$WP_TESTS_DIR"/wp-tests-config.php
        sed -i "s/yourpasswordhere//" "$WP_TESTS_DIR"/wp-tests-config.php
        sed -i "s|localhost|${DB_HOST}|" "$WP_TESTS_DIR"/wp-tests-config.php
        echo "  ✓ Configuration file created and updated (password left empty)"
    fi
    
    echo "✓ Test suite installed successfully!"
    echo ""
}

install_db() {
    if [ ${SKIP_DB_CREATE} = "true" ]; then
        echo "⊘ Skipping database creation (SKIP_DB_CREATE=true)"
        echo ""
        return 0
    fi

    echo "Creating test database..."

    # Test MySQL connection first
    if [ -z "$DB_PASS" ]; then
        if ! mysql -u"$DB_USER" -h"$DB_HOST" -e "SELECT 1" > /dev/null 2>&1; then
            echo "✗ Failed to connect to MySQL. Please check your credentials."
            exit 1
        fi
    else
        if ! mysql -u"$DB_USER" -p"$DB_PASS" -h"$DB_HOST" -e "SELECT 1" > /dev/null 2>&1; then
            echo "✗ Failed to connect to MySQL. Please check your credentials."
            exit 1
        fi
    fi

    # Create the database
    if [ -z "$DB_PASS" ]; then
        if mysql -u"$DB_USER" -h"$DB_HOST" -e "CREATE DATABASE IF NOT EXISTS $DB_NAME" 2>/dev/null; then
            echo "✓ Database '$DB_NAME' created successfully!"
        else
            echo "⚠ Database '$DB_NAME' may already exist or creation failed"
            echo "  This is usually okay if the database already exists"
        fi
    else
        if mysql -u"$DB_USER" -p"$DB_PASS" -h"$DB_HOST" -e "CREATE DATABASE IF NOT EXISTS $DB_NAME" 2>/dev/null; then
            echo "✓ Database '$DB_NAME' created successfully!"
        else
            echo "⚠ Database '$DB_NAME' may already exist or creation failed"
            echo "  This is usually okay if the database already exists"
        fi
    fi
    echo ""
}

install_wp
install_test_suite
install_db

echo "======================================"
echo "✓ Installation Complete!"
echo "======================================"
echo ""
echo "Configuration is stored in tests/config.php"
echo "Database password is NOT stored (secure!)"
echo ""
echo "Run your tests:"
echo "  composer test"
echo ""
echo "======================================"