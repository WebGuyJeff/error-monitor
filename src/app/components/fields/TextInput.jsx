import { FormField } from './FormField'
import PropTypes from 'prop-types'

/**
 * Text input
 */
const TextInput = ( {
	label,
	value,
	onChange,
	type = 'text',
	description,
	classes = '',
	invalid = false,
	attrs = {},
	fieldKey,
} ) => (
	<FormField
		label={label}
		description={description}
		classes={classes}
		invalid={invalid}
		fieldKey={fieldKey}
	>
		<input
			id={fieldKey ? `${fieldKey}-id` : undefined}
			aria-labelledby={fieldKey ? `${fieldKey}-aria` : undefined}
			type={type}
			value={value ?? ''}
			onChange={onChange}
			className={invalid ? 'em-invalid' : ''}
			aria-invalid={invalid ? 'true' : undefined}
			{...attrs}
		/>
	</FormField>
)

TextInput.propTypes = {
	label: PropTypes.string.isRequired,
	value: PropTypes.oneOfType( [ PropTypes.string, PropTypes.number ] ),
	onChange: PropTypes.func.isRequired,
	type: PropTypes.string,
	description: PropTypes.string,
	classes: PropTypes.string,
	invalid: PropTypes.bool,
	attrs: PropTypes.object,
	fieldKey: PropTypes.string.isRequired,
}

export { TextInput }
