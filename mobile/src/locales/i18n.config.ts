import i18n from 'i18next';
import { initReactI18next } from 'react-i18next';
import { APP_CONFIG, STORAGE_KEYS } from '../shared/constants/config';
import { storageService } from '../shared/services/storage/mmkv.service';
import en from './en.json';
import si from './si.json';

const initI18n = async () => {
  const savedLanguage = await storageService.getItem(STORAGE_KEYS.LANGUAGE);

  i18n.use(initReactI18next).init({
    compatibilityJSON: 'v3',
    resources: {
      en: { translation: en },
      si: { translation: si },
    },
    lng: savedLanguage || APP_CONFIG.DEFAULT_LANGUAGE,
    fallbackLng: APP_CONFIG.DEFAULT_LANGUAGE,
    interpolation: {
      escapeValue: false,
    },
  });
};

export const changeLanguage = async (language: string) => {
  await storageService.setItem(STORAGE_KEYS.LANGUAGE, language);
  i18n.changeLanguage(language);
};

initI18n();

export default i18n;
