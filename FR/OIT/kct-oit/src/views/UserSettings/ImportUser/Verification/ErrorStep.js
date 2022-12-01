import React from 'react';
import {Button} from '@material-ui/core';


/**
 * @class
 * @component
 *
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This component is developed to render an error page with  a button to go back(previous step) to fix
 * all the issues.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Object} props Inherited from parent component
 * @param {Object} props.data Data used for error message
 * @param {Function} props.handleBack Function used for go back
 * @return {JSX.Element}
 */
const ErrorStep = (props) => {
    const {error} = props.data;

    return (
        <div>
            {error.map((item) => {
                return <div className="text-danger">{item}</div>
            })}
            <div className="VerifyDivBackBtn">
                <Button color="primary" variant="contained" onClick={props.handleBack}>
                    Back
                </Button>
            </div>
        </div>
    )
}

export default ErrorStep;   