/**
 * @type {Object}
 * @property {Object} error Error object that contain error message with a status code
 * @property {String} error.message Error message
 * @property {Object} error.response Response object
 * @property {Number} error.response.status Error response status
 * @property {String} error.response.data.message Error response message 
 */

 const ErrorObj = {
    message: 'message',
    response: {
        status: 500,
        data:{
            message:`message`
        }
    }
}

export default ErrorObj;