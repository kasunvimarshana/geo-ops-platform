import i18n from 'i18next';
import { initReactI18next } from 'react-i18next';
import AsyncStorage from '@react-native-async-storage/async-storage';
import en from './en';
import si from './si';

const LANGUAGE_STORAGE_KEY = 'app_language';

i18n
  .use(initReactI18next)
  .init({
    resources: {
      en: { translation: en },
      si: { translation: si },
    },
    lng: 'en',
    fallbackLng: 'en',
    interpolation: {
      escapeValue: false,
    },
    react: {
      useSuspense: false,
    },
  });

// Load saved language preference
AsyncStorage.getItem(LANGUAGE_STORAGE_KEY).then((savedLanguage) => {
  if (savedLanguage && (savedLanguage === 'en' || savedLanguage === 'si')) {
    i18n.changeLanguage(savedLanguage);
  }
});

// Save language preference on change
i18n.on('languageChanged', (lng) => {
  AsyncStorage.setItem(LANGUAGE_STORAGE_KEY, lng);
});

export default i18n;
