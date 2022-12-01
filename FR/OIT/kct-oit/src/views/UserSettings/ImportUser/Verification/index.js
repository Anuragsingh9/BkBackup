import React from 'react';
import ErrorStep from './ErrorStep';
import Verify from './Verify.js';



/**
 * @class
 * @component
 *
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This is a wrapper component of verification step(import user process).This component will decide to
 * render verification step or an error page component just after 2nd step(match field).
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Object} props Props passed from parent component
 * @param {Object} props.data All data fetched from API response
 * @param {User} props.data.users All imported users data from csv file
 * @param {String} props.data.event_uuid Event uuid in which users need to be added
 * @param {Function} props.handleBack This method will allow user to go one step back in import user process
 * @param {Function} props.handleNext This method will allow user to go one step forward in import user process
 * @return {JSX.Element}
 */
const Verification = (props) => {
    return (
        <div>
            {props.data.error ?
                <ErrorStep
                    handleBack={props.handleBack}
                    error={props.data.error}
                    {...props}
                />
                :
                <Verify
                    handleBack={props.handleBack}
                    handleNext={props.handleNext}
                    users={props.data.users}
                    callBack={props.callBack}
                    {...props}
                />
            }
        </div>
    )
}

export default Verification;