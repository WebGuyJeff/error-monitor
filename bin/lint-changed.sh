#!/usr/bin/env bash
set -euo pipefail

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
cd "${ROOT_DIR}"
IGNORE_FILE=".phpcs-changed-ignore"

if [[ ! -x "./vendor/bin/phpcs" ]]; then
	echo "PHPCS not found. Run: composer install"
	exit 1
fi

# Track changed files in this working tree:
# - staged and unstaged tracked changes vs HEAD.
# - untracked files not ignored by git.
tracked_changes="$(git diff --name-only HEAD --diff-filter=ACMRTUXB || true)"
untracked_changes="$(git ls-files --others --exclude-standard || true)"

all_changes="$(printf "%s\n%s\n" "${tracked_changes}" "${untracked_changes}" | sed '/^$/d' | sort -u)"

declare -a files=()
while IFS= read -r file; do
	if [[ "${file}" == *.php && -f "${file}" ]]; then
		files+=("${file}")
	fi
done <<< "${all_changes}"

if [[ ${#files[@]} -eq 0 ]]; then
	echo "No existing changed PHP files found."
	exit 0
fi

declare -a lint_targets=()
if [[ -f "${IGNORE_FILE}" ]]; then
	while IFS= read -r file; do
		skip=false
		while IFS= read -r ignored; do
			ignored_trimmed="$(echo "${ignored}" | sed 's/[[:space:]]*$//')"
			if [[ -z "${ignored_trimmed}" || "${ignored_trimmed}" == \#* ]]; then
				continue
			fi
			if [[ "${file}" == "${ignored_trimmed}" ]]; then
				skip=true
				break
			fi
		done < "${IGNORE_FILE}"
		if [[ "${skip}" == false ]]; then
			lint_targets+=("${file}")
		fi
	done < <(printf "%s\n" "${files[@]}")
else
	lint_targets=("${files[@]}")
fi

if [[ ${#lint_targets[@]} -eq 0 ]]; then
	echo "No lint target PHP files found after ignore filtering."
	exit 0
fi

echo "Running PHPCS on changed PHP files:"
printf ' - %s\n' "${lint_targets[@]}"

./vendor/bin/phpcs -ps --standard=phpcs.xml.dist "${lint_targets[@]}"
