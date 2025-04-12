/* global jest, test, expect */

import React from 'react';
import { render } from '@testing-library/react';

import ImageTextGeneratorField from '../ImageTextGeneratorField';

test('ImageTextGeneratorField renders', async () => {
  const { container } = render(<ImageTextGeneratorField/>);
  const options = container.querySelectorAll('.image-text-generator');
  expect(options).toHaveLength(1);
});

test('ImageTextGeneratorField renders with props', async () => {
  const props = {
    id: 'test-id',
    value: 'test-value',
    extraClass: 'extra-class',
    imageID: 123,
    actions: {
      onSubmit: jest.fn(),
    },
  };
  const { container } = render(<ImageTextGeneratorField {...props} />);
  const input = container.querySelector('input[type="text"]');
  const button = container.querySelector('button');

  expect(input).toBeTruthy();
  expect(input.value).toBe(props.value);
  expect(input.id).toBe(props.id);
  expect(button).toBeTruthy();
  expect(button.className).toContain('btn-primary');
  expect(button.textContent).toBe('Generate Alt Text');
  expect(button.disabled).toBe(false);
  expect(container.querySelector('.image-text-generator').className).toContain(props.extraClass);
  expect(container.querySelector('.image-text-generator').className).toContain('image-text-generator');
  expect(container.querySelector('.image-text-generator').className).toContain('extra-class');
  expect(container.querySelector('.image-text-generator').className).toContain('text');
  expect(container.querySelector('.image-text-generator').className).toContain('generate-button');
  expect(container.querySelector('.image-text-generator').className).toContain('btn');
  expect(container.querySelector('.image-text-generator').className).toContain('btn-primary');

  // Simulate button click
  button.click();
  expect(props.actions.onSubmit).toHaveBeenCalledWith(props.id, props.value);
  // Simulate input change
  input.value = 'new value';
  input.dispatchEvent(new Event('change'));

  expect(props.actions.onSubmit).toHaveBeenCalledWith(props.id, 'new value');
});
