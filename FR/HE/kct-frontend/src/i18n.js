import i18n from 'i18next';
import {initReactI18next} from 'react-i18next';
import Backend from 'i18next-http-backend';
import LanguageDetector from 'i18next-browser-languagedetector';
import Helper from './Helper';
// for passing in lng and translations on init

const Languages = ['en', 'fr'];

const currentLang = Helper.currLang ? Helper.currLang.toLocaleLowerCase() : 'fr';


i18n
    .use(Backend)
    // detect user language
    // learn more: https://github.com/i18next/i18next-browser-languageDetector
    .use(LanguageDetector)
    // pass the i18n instance to react-i18next.
    .use(initReactI18next)
    // init i18next
    // for all options read: https://www.i18next.com/overview/configuration-options
    .init({
        lng: currentLang,
        react: {
            useSuspense: false,
            wait: true,
        },
        fallbackLng: currentLang,
        debug: false,
        whitelist: Languages,
        interpolation: {
            escapeValue: false, // not needed for react as it escapes by default
        },
        nsSeperator: false,
        keySeperator: false,
        backend: {
            loadPath: '/e/locales/{{lng}}/{{ns}}.json',
        },
    });

export default i18n;

