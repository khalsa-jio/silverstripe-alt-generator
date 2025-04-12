import React, { useState, useEffect } from 'react';
import { inject } from 'lib/Injector';
import PropTypes from 'prop-types';
import classNames from 'classnames';
import { Input, InputGroup, InputGroupAddon } from 'reactstrap';

const ImageTextGeneratorField = (props) => {
  const {
    id,
    value,
    name,
    extraClass,
    className,
    disabled,
    readOnly,
    placeholder,
    autoFocus,
    type,
    maxLength,
    attributes,
    onChange,
    onBlur,
    onFocus,

    // props for alt-text generation
    imageID,
    icon,

    // injected props
    FieldGroup,
    Button,
  } = props;

  const [loading, setLoading] = useState(false);
  const [displayText, setDisplayText] = useState('');
  const [targetText, setTargetText] = useState('');

  // Sync with external value changes
  useEffect(() => {
    setDisplayText(value || '');
    if (!value) setTargetText('');
  }, [value]);

  const handleChange = (event) => {
    if (onChange && event.target) {
      setDisplayText(event.target.value);
      onChange(event, { id, value: event.target.value });
    }
  };

  // For typing effect
  useEffect(() => {
    if (!targetText) return;

    let index = 0;
    const speed = 30;

    const textType = () => {
      if (index < targetText.length) {
        setDisplayText(targetText.substring(0, index + 1));
        index += 1;
        setTimeout(textType, speed);
      } else {
        handleChange({ target: { value: targetText } });
      }
    };

    textType();
  }, [targetText]);

  const getInputProps = () => {
    const inputProps = {
      className: `${className} ${extraClass}`,
      id,
      disabled,
      name,
      maxLength,
      'aria-label': name,
      readOnly,
      value: displayText || value || '',
      placeholder,
      autoFocus,
      type: type || 'text',
      onBlur,
      onFocus,
      ...(attributes || {}),
    };

    if (!readOnly) {
      inputProps.onChange = handleChange;
    }

    return inputProps;
  };

  const handleGenerate = async () => {
    setLoading(true);
    try {
      const response = await fetch(
        `admin/alt-generator/generate/${imageID}`,
        { method: 'POST',
          headers: {
            'X-Requested-With': 'XMLHttpRequest', // Ajax request
            'Content-Type': 'application/json'
          },
        }
      );
      const data = await response.json();

      if (data.altText) {
        setTargetText(data.altText);
      }
    } catch (error) {
      console.error('Generation failed:', error); // eslint-disable-line no-console
    }
    setLoading(false);
  };

  const buttonClasses = [
    'image-alt--input-group',
    'btn btn--last',
    'btn-outline-secondary',
    props.extraClass
  ];

  const fieldGroupProps = {
    ...props,
    className: classNames('image-text-generator-field', extraClass),
  };

  return (
    <FieldGroup {...fieldGroupProps}>
      <InputGroup>
        <Input {...getInputProps()} />
        <InputGroupAddon addonType="append">
          <Button
            onClick={handleGenerate}
            disabled={loading || disabled}
            loading={loading}
            noText
            className={classNames(buttonClasses)}
            icon={icon}
          />
        </InputGroupAddon>
      </InputGroup>
    </FieldGroup>
  );
};

ImageTextGeneratorField.propTypes = {
  extraClass: PropTypes.string,
  id: PropTypes.string,
  className: PropTypes.string,
  disabled: PropTypes.bool,
  readOnly: PropTypes.bool,
  placeholder: PropTypes.string,
  autoFocus: PropTypes.bool,
  type: PropTypes.string,
  attributes: PropTypes.object,
  onChange: PropTypes.func,
  onBlur: PropTypes.func,
  onFocus: PropTypes.func,
  imageID: PropTypes.number,
};

ImageTextGeneratorField.defaultProps = {
  extraClass: '',
  className: '',
  value: '',
  type: 'text',
  attributes: {},
};

export default inject(
  ['FieldGroup', 'Button']
)(ImageTextGeneratorField);
