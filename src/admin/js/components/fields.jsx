import { createElement } from '@wordpress/element'

const Field = ({ children, classes = '' }) =>
	createElement('div', { className: `field ${classes}`.trim() }, children)

const TextInput = ({ label, value, onChange, type = 'text', description, classes = '', invalid = false, attrs = {} }) =>
	createElement(
		Field,
		{ classes: `field-singleline ${classes}` },
		createElement('label', null, label),
		createElement('input', {
			type,
			value: value ?? '',
			onChange,
			className: invalid ? 'em-invalid' : '',
			'aria-invalid': invalid ? 'true' : undefined,
			...attrs,
		}),
		description ? createElement('p', { className: 'field__description' }, description) : null,
	)

const SelectInput = ({ label, value, onChange, options, description, classes = '', invalid = false }) =>
	createElement(
		Field,
		{ classes: `field-singleline field-select ${classes}` },
		createElement('label', null, label),
		createElement(
			'select',
			{
				value: value ?? '',
				onChange,
				className: invalid ? 'em-invalid' : '',
				'aria-invalid': invalid ? 'true' : undefined,
			},
			Object.entries(options).map(([optionValue, optionLabel]) =>
				createElement('option', { key: optionValue, value: optionValue }, optionLabel),
			),
		),
		description ? createElement('p', { className: 'field__description' }, description) : null,
	)

const ToggleInput = ({ label, checked, onChange, description }) =>
	createElement(
		Field,
		{ classes: 'field-toggle' },
		createElement('input', {
			type: 'checkbox',
			checked: !!checked,
			onChange,
		}),
		createElement('label', null, label),
		description ? createElement('p', { className: 'field__description' }, description) : null,
	)

export { Field, TextInput, SelectInput, ToggleInput }
