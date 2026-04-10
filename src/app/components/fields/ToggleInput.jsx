import { FormField } from './FormField'
import PropTypes from 'prop-types'

/**
 * Toggle (checkbox) input
 */
const ToggleInput = ( {
	label,
	checked,
	onChange,
	description,
	classes = '',
	fieldKey,
} ) => (
	<FormField
		label={label}
		description={description}
		classes={classes}
		layout="toggle"
		fieldKey={fieldKey}
	>
		<input
			id={fieldKey ? `${fieldKey}-id` : undefined}
			aria-labelledby={fieldKey ? `${fieldKey}-aria` : undefined}
			type="checkbox"
			checked={Boolean( checked )}
			onChange={onChange}
		/>
	</FormField>
)

ToggleInput.propTypes = {
	label: PropTypes.string.isRequired,
	checked: PropTypes.oneOfType( [ PropTypes.bool, PropTypes.number ] ),
	onChange: PropTypes.func.isRequired,
	description: PropTypes.string,
	classes: PropTypes.string,
	fieldKey: PropTypes.string.isRequired,
}

export { ToggleInput }
