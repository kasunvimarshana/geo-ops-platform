/**
 * Storage Configuration
 * Keys for local storage (SQLite, MMKV)
 */

export const STORAGE_KEYS = {
  AUTH_TOKEN: "auth_token",
  REFRESH_TOKEN: "refresh_token",
  USER_DATA: "user_data",
  LANGUAGE: "language",
  THEME: "theme",
  GPS_TRACKS: "gps_tracks",
  OFFLINE_QUEUE: "offline_queue",
  SETTINGS: "settings",
};

/**
 * Generate a secure encryption key if not provided
 * In production, this should be set in environment variables
 * WARNING: Using crypto.getRandomValues for secure random generation
 */
const generateSecureKey = (): string => {
  if (typeof crypto !== "undefined" && crypto.getRandomValues) {
    // Use Web Crypto API for secure random generation
    const array = new Uint8Array(32);
    crypto.getRandomValues(array);
    return Array.from(array, (byte) => byte.toString(16).padStart(2, "0")).join(
      "",
    );
  }

  // Fallback for environments without crypto API
  // This is not cryptographically secure and should not be used in production
  const timestamp = Date.now().toString();
  const random = Math.random().toString(36).substring(2, 15);
  console.warn(
    "⚠️ Using non-cryptographic random key generation. Set EXPO_PUBLIC_STORAGE_KEY in production.",
  );
  return `${timestamp}-${random}`;
};

export const MMKV_CONFIG = {
  id: "geo-ops-storage",
  // Use environment variable or generate a secure fallback key
  // WARNING: Generating key at runtime means data won't persist across app reinstalls
  // For production, always set EXPO_PUBLIC_STORAGE_KEY in environment
  encryptionKey: process.env.EXPO_PUBLIC_STORAGE_KEY || generateSecureKey(),
};

// Warn in development if encryption key is not properly configured
if (__DEV__ && !process.env.EXPO_PUBLIC_STORAGE_KEY) {
  console.warn(
    "⚠️ MMKV encryption key not configured. Using generated fallback key. " +
      "Set EXPO_PUBLIC_STORAGE_KEY in .env for production.",
  );
}

export default STORAGE_KEYS;
