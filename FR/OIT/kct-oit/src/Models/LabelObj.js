/**
 * @type {Object}
 * @property {String} value label current value
 * @property {String} locale Language - EN/FR
 */
let locals = {
    value: "Business Team CHECK1",
    locale: "en"
}

/**
 * @type {Object}
 * @property {Number} group_id Unique ID of a group
 * @property {locals[]} labels Labels Array of object where each object contain label value in french/english
 * @property {String} method Method name eg - GET/POST
 * @example {value: "Business Team CHECK1",locale: "en"}
 */

const LabelObj = {
    group_id: 1,
    labels: [
        {
            name: "business_team",
            locales: [
                {
                    value: "Business Team CHECK1",
                    locale: "en"
                },
                {
                    value: "Business Team TEST",
                    locale: "fr"
                }
            ]
        }
    ],
    method: "POST"
}
