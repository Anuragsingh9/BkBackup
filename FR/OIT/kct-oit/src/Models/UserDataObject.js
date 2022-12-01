
/**
 * @type {Object}
 * @property {String} id User ID
 * @property {String} fname User first name
 * @property {String} lname User last name 
 * @property {String} email User email
 * @property {String} avatar User avatar URL
 * @property {String} phone_code User phone code
 * @property {String} phone_number User phone number
 * @property {String} mobile_code User mobile code
 * @property {String} mobile_number User mobile number
 * @property {Array} mobiles User mobiles number array
 * @property {Array} phones User phone number array
 * @property {String} setting  user setting text
 * @property {Object} current_group User current group object
 * @property {Number} current_group.id User current group ID
 * @property {String} current_group.group_name User current group name
 * @property {String} current_group.group_short_name User current group short name
 * @property {String} current_group.description user current group description text
 * @property {Number} current_group.is_default Current group default value
 * @property {Number} is_organiser  organizer state
 * @property {Array} roles  Array of current user roles
 * @property {String} city  City name
 * @property {String} country  Country name
 * @property {String} address  User address
 * @property {String} postal User postal code
 * @property {Object} company  User company name
 * @property {Number} company.id User company ID
 * @property {Number} company.entity_type_id  User entity type ID
 * @property {String} company.long_name User company long name
 * @property {String} company.position  User company position
 * @property {Array} unions Array of available unions
 * @property {Number} internal_id Inter ID
 * @property {Number} is_self User is self
 * @property {String} login_count Login count
 */
const UserDataObject = {
    "id": 1,
    "fname": "Nitin",
    "lname": "Ranga",
    "email": "nitinhumann@mailinator.com",
    "avatar": "https://s3.eu-west-2.amazonaws.com/kct-dev/nitinhumann.humannconnect.dev/users/avatar/K4Ji3FxgiUhuFQgUKwRTanASNQG8nGZOwLeUe8gl.png",
    "phone_code": null,
    "phone_number": "+500453433343544",
    "mobile_code": null,
    "mobile_number": "+33 3 45 34 54 34",
    "mobiles": [
        {
            "country_code": null,
            "number": "+33 3 45 34 54 34",
            "is_primary": 1,
            "type": 1
        }
    ],
    "phones": [
        {
            "country_code": null,
            "number": "+500453433343544",
            "is_primary": 1,
            "type": 2
        }
    ],
    "setting": {
        "lang": "EN"
    },
    "current_group": {
        "id": 1,
        "group_name": "HCTT",
        "group_short_name": "HCTT",
        "description": null,
        "is_default": 1
    },
    "is_organiser": 1,
    "roles": [
        "org_admin",
        "organiser_main"
    ],
    "city": null,
    "country": null,
    "address": null,
    "postal": null,
    "company": {
        "id": 10,
        "entity_type_id": 1,
        "long_name": "KCT Technologies",
        "position": "Frontier"
    },
    "unions": [
        {
            "id": 6,
            "entity_type_id": 2,
            "long_name": "Cvcvbvbcbcvb",
            "position": "Ddfgfgdgddcvvbc43543"
        },
        {
            "id": 11,
            "entity_type_id": 2,
            "long_name": "FrontEnd",
            "position": "Employee"
        },
        {
            "id": 4,
            "entity_type_id": 2,
            "long_name": "Cvbvcbcbcbc",
            "position": "Ddfgfgdgddcvvbccvcbvcb"
        }
    ],
    "internal_id": "3434354",
    "is_self": 1,
    "login_count": 1
}
export default UserDataObject