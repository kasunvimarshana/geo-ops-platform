import React, { useState } from 'react';
import { View, Text, StyleSheet, TouchableOpacity } from 'react-native';
import { useTranslation } from 'react-i18next';
import { changeLanguage } from '../../locales/i18n.config';
import { colors } from '../../theme/colors';
import { typography } from '../../theme/typography';
import { spacing } from '../../theme/spacing';

export const LanguageSwitcher: React.FC = () => {
  const { i18n } = useTranslation();
  const [currentLang, setCurrentLang] = useState(i18n.language);

  const switchLanguage = async (lang: string) => {
    await changeLanguage(lang);
    setCurrentLang(lang);
  };

  return (
    <View style={styles.container}>
      <TouchableOpacity
        style={[styles.button, currentLang === 'en' && styles.activeButton]}
        onPress={() => switchLanguage('en')}
      >
        <Text style={[styles.text, currentLang === 'en' && styles.activeText]}>
          English
        </Text>
      </TouchableOpacity>
      <TouchableOpacity
        style={[styles.button, currentLang === 'si' && styles.activeButton]}
        onPress={() => switchLanguage('si')}
      >
        <Text style={[styles.text, currentLang === 'si' && styles.activeText]}>
          සිංහල
        </Text>
      </TouchableOpacity>
    </View>
  );
};

const styles = StyleSheet.create({
  container: {
    flexDirection: 'row',
    gap: spacing.sm,
  },
  button: {
    paddingVertical: spacing.xs,
    paddingHorizontal: spacing.md,
    borderRadius: 20,
    backgroundColor: colors.surface,
    borderWidth: 1,
    borderColor: colors.border,
  },
  activeButton: {
    backgroundColor: colors.primary,
    borderColor: colors.primary,
  },
  text: {
    ...typography.body2,
    color: colors.text.secondary,
  },
  activeText: {
    color: colors.text.white,
    fontWeight: '600',
  },
});
