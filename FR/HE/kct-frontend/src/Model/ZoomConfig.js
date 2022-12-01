/**
 * @type {Object}
 * @property {String} apiKey Api Key sent from backend
 * @property {String} signature Signature prepared from backend
 * @property {String} meetingNumber Id of the zoom meeting
 * @property {String} userName Current user name to displayed moderator
 * @property {String} password Password for zoom meeting join (if any)
 * @property {String} userEmail Email of the user to join zoom meeting

 */
const ZoomConfig = {
    apiKey: 'apiKey',
    signature: 'signature',
    meetingNumber: 'meetingNumber',
    userName: 'userName',
    password: 'password',
    userEmail: 'userEmail',
};
export default ZoomConfig;