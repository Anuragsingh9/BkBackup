import KCTLocale from './KeepContactlLocales';

const lang = localStorage.getItem("current_lang") ? localStorage.getItem("current_lang") : 'FR';
const KCTLocales = KCTLocale[lang.toUpperCase()];

export {
    KCTLocales
}