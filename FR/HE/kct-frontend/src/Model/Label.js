/**
 * @type {Object}
 * @property {String} value Value of label for current locale
 * @property {String} locale Locale name of label
 */
// eslint-disable-next-line no-unused-vars
const Locale = {
    value: 'value',
    locale: 'locale',
};


/**
 * @type {Object}
 * @property {String} name Name of label
 * @property {Locale[]} locales Available locales for the current label
 *
 */
const Label = {
    name: 'name',
    locales: [
        {
            value: 'value',
            locale: 'en',
        },
    ],
};


export default Label;
