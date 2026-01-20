import i18n from 'i18next';
import { initReactI18next } from 'react-i18next';
import * as Localization from 'expo-localization';
import AsyncStorage from '@react-native-async-storage/async-storage';
import en from './en';
import si from './si';

const LANGUAGE_STORAGE_KEY = 'user_language';

// Initialize i18n
i18n
  .use(initReactI18next)
  .init({
    compatibilityJSON: 'v3',
    resources: {
      en: { translation: en },
      si: { translation: si },
    },
    lng: Localization.locale.split('-')[0], // Default to device locale
    fallbackLng: 'si', // Fallback to Sinhala (primary language)
    interpolation: {
      escapeValue: false,
    },
  });

// Load saved language preference
AsyncStorage.getItem(LANGUAGE_STORAGE_KEY).then((savedLanguage) => {
  if (savedLanguage && (savedLanguage === 'en' || savedLanguage === 'si')) {
    i18n.changeLanguage(savedLanguage);
  }
});

// Save language preference when changed
i18n.on('languageChanged', (lng) => {
  AsyncStorage.setItem(LANGUAGE_STORAGE_KEY, lng);
});

export default i18n;
