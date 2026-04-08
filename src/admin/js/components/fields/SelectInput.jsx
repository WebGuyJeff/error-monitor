import { FormField } from './FormField'
import PropTypes from 'prop-types'

/**
 * Select input
 */
const SelectInput = ( {
	label,
	value,
	onChange,
	options,
	description,
	classes = '',
	invalid = false,
	fieldKey,
} ) => (
	<FormField
		label={label}
		description={description}
		classes={classes}
		invalid={invalid}
		layout="select"
		fieldKey={fieldKey}
	>
		<select
			id={fieldKey ? `${fieldKey}-id` : undefined}
			aria-labelledby={fieldKey ? `${fieldKey}-aria` : undefined}
			value={value ?? ''}
			onChange={onChange}
			className={invalid ? 'em-invalid' : ''}
			aria-invalid={invalid ? 'true' : undefined}
		>
			{Object.entries( options ).map( ( [ optionValue, optionLabel ] ) => (
				<option key={optionValue} value={optionValue}>
					{optionLabel}
				</option>
			) )}
		</select>
	</FormField>
)

SelectInput.propTypes = {
	label: PropTypes.string.isRequired,
	value: PropTypes.oneOfType( [ PropTypes.string, PropTypes.number ] ),
	onChange: PropTypes.func.isRequired,
	options: PropTypes.objectOf(
		PropTypes.oneOfType( [ PropTypes.string, PropTypes.number ] )
	).isRequired,
	description: PropTypes.string,
	classes: PropTypes.string,
	invalid: PropTypes.bool,
	fieldKey: PropTypes.string.isRequired,
}

export { SelectInput }
