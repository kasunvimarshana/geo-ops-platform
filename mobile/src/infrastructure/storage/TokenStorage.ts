/**
 * Token Storage
 * Secure storage for authentication tokens using MMKV
 */

import { MMKV } from 'react-native-mmkv';
import { STORAGE_KEYS, MMKV_CONFIG } from '../../config/storage';

const storage = new MMKV({
  id: MMKV_CONFIG.id,
  encryptionKey: MMKV_CONFIG.encryptionKey,
});

export const getToken = async (): Promise<string | null> => {
  try {
    return storage.getString(STORAGE_KEYS.AUTH_TOKEN) || null;
  } catch (error) {
    console.error('Error getting token:', error);
    return null;
  }
};

export const saveToken = async (token: string): Promise<void> => {
  try {
    storage.set(STORAGE_KEYS.AUTH_TOKEN, token);
  } catch (error) {
    console.error('Error saving token:', error);
  }
};

export const getRefreshToken = async (): Promise<string | null> => {
  try {
    return storage.getString(STORAGE_KEYS.REFRESH_TOKEN) || null;
  } catch (error) {
    console.error('Error getting refresh token:', error);
    return null;
  }
};

export const saveRefreshToken = async (token: string): Promise<void> => {
  try {
    storage.set(STORAGE_KEYS.REFRESH_TOKEN, token);
  } catch (error) {
    console.error('Error saving refresh token:', error);
  }
};

export const clearToken = async (): Promise<void> => {
  try {
    storage.delete(STORAGE_KEYS.AUTH_TOKEN);
    storage.delete(STORAGE_KEYS.REFRESH_TOKEN);
    storage.delete(STORAGE_KEYS.USER_DATA);
  } catch (error) {
    console.error('Error clearing tokens:', error);
  }
};

export default storage;
