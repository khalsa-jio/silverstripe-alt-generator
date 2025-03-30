import React, { useState, useEffect } from 'react';
import PropTypes from 'prop-types';
import { Input, InputGroup, InputGroupAddon } from 'reactstrap';

const InputField = (props) => {
  const {
    id,
    value,
    extraClass,
    className,
    disabled,
    readOnly,
    placeholder,
    autoFocus,
    type,
    attributes,
    onChange,
    onBlur,
    onFocus,
    // New props for alt-text generation
    imageID,
  } = props;

  // State for loading and typing animation
  const [loading, setLoading] = useState(false);
  const [displayText, setDisplayText] = useState('');
  const [targetText, setTargetText] = useState('');

  // Sync with external value changes
  useEffect(() => {
    setDisplayText(value || '');
    if (!value) setTargetText('');
  }, [value]);

  // Typing animation effect
  useEffect(() => {
    if (!targetText) return;
    
    let index = 0;
    const speed = 30;
    
    const type = () => {
      if (index < targetText.length) {
        setDisplayText(targetText.substring(0, index + 1));
        index++;
        setTimeout(type, speed);
      } else {
        handleChange({ target: { value: targetText } });
      }
    };
    
    type();
  }, [targetText]);

  const getInputProps = () => {
    const inputProps = {
      className: `${className} ${extraClass}`,
      id,
      disabled,
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

  const handleChange = (event) => {
    if (onChange && event.target) {
      setDisplayText(event.target.value);
      onChange(event, { id, value: event.target.value });
    }
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
      console.error('Generation failed:', error);
    }
    setLoading(false);
  };

  return (
    <InputGroup>
      <Input {...getInputProps()} />
      <InputGroupAddon addonType="append">
        <button
          type="button"
          className="btn btn-primary generate-button"
          onClick={handleGenerate}
          disabled={loading || disabled}
        >
          {loading ? (
            <span className="loading-dots">Generating</span>
          ) : (
            '✨ Generate Alt Text'
          )}
        </button>
      </InputGroupAddon>
    </InputGroup>
  );
};

InputField.propTypes = {
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

InputField.defaultProps = {
  extraClass: '',
  className: '',
  value: '',
  type: 'text',
  attributes: {},
};

export default InputField;