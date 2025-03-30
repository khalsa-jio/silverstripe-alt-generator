import Injector from 'lib/Injector';
import ImageTextGeneratorField from '../components/ImageTextGeneratorField/ImageTextGeneratorField';

export default () => {
  Injector.component.registerMany({
    ImageTextGeneratorField,
  });
};
