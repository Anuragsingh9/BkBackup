
/**
 * @type {Object}
 * @property {Number} id User Id of user
 * @property {String} fname First name of the user
 * @property {String} lname Last name of the user
 * @property {String} email Email of the user
 * @property {String} address Address of the user
 * @property {String} avatar Profile picture of the user
 * @property {String} city City name of the user
 * @property {Number} phone Phone number of the user
 * @property {Number} is_organiser To check if the user is organiser
 * @property {Number} login_count To check if the user is logging first time
 * @property {Object} setting User's language
 * @property {String} setting.lang Currently selected language example:- en
 * @property {String} company Company of the user associated with
 * @property {String} union Union of the user associated with
 */
const User = {
    id: 3,
    fname: "Taylor",
    lname: "Otwell",
    email: "taylor@mailinator.com",
    address: "123, A society,Country",
    avatar:"https://s3.eu-west-2.amazonaws.com/kct-dev/testingaccount.humannconnect.dev/users/avatar/0c5dd3c1-2148-4fad-95e1-fbbed20f5067.",
    city: "Alaska",
    phone: "+91987654321",
    is_organiser: 1,
    login_count:1,
    setting: {
        lang:"en"
    },
    company:"HumannConnect",
    union:"Andy Brown Union"
}

export default User;