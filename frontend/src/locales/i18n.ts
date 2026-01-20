import i18next from 'i18next';
import { initReactI18next } from 'react-i18next';
import { en, es, si } from './index';

const resources = {
  en: { translation: en },
  es: { translation: es },
  si: { translation: si },
};

i18next.use(initReactI18next).init({
  resources,
  lng: 'en',
  fallbackLng: 'en',
  interpolation: {
    escapeValue: false,
  },
});

export default i18next;
