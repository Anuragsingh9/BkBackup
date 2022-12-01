/**
 *
 * @type {Object}
 * @property {String} space_uuid, Space uuid of the space
 * @property {String} space_mood, Space name
 * @property {String} space_short_name, Space short name
 * @property {Number} max_capacity, Maximum capacity of a space
 * @property {String} event_uuid, Event uuid to which the space is associated
 * @property {Number} is_default, To indicate if space is default space or not
 * @property {User} space_host, User data of the space host
 * @property {Number} is_vip_space, To check if space is VIP space
 * @property {String} space_name, Name of the space
 * @property {Number} is_self_header, To check if event should have own header info or it should have default header info
 * @property {String} header_line_1 Header line one of the event
 * @property {String} header_line_2, Header line two of the event
 * }
 */
const Space = {
    "space_uuid": "a814765c-fc35-11ec-bcde-0a502021c365",
    "space_name": "Welcome Space",
    "space_short_name": "Anything you can write",
    "space_mood": "Welcome Space",
    "max_capacity": 1000,
    "is_vip_space": 0,
    "event_uuid": "a81292d8-fc35-11ec-8134-0a502021c365",
    "order_id": "b",
    "space_hosts": [
        {
            "id": 1,
            "fname": "Staging",
            "lname": "Testing",
            "email": "stagingtesting@mailinator.com",
            "avatar": "https://s3.eu-west-2.amazonaws.com/kct-dev/testingaccount.humannconnect.dev/users/avatar/ce17443a-24f9-42fa-8fbc-3aaaa54c543a.",
            "company": {
                "id": 1,
                "entity_type_id": 1,
                "long_name": "Staging Server",
                "position": null
            },
            "union": []
        }
    ],
    "header_line_1": "",
    "header_line_2": "",
    "is_self_header": 0,
    "is_default": 1
}
export default Space;