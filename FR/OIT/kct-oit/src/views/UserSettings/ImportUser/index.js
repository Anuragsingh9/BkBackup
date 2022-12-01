import React, {useState} from 'react';
import _ from 'lodash';
import {makeStyles} from '@material-ui/core/styles';
import Typography from '@material-ui/core/Typography';
import Stepper from '@material-ui/core/Stepper';
import Step from '@material-ui/core/Step';
import StepLabel from '@material-ui/core/StepLabel';
import Button from '@material-ui/core/Button';
import MatchField from './MatchFields/index.js';
import FileUpload from './FileUpload.js';
import Verification from './Verification/index.js';

//Prepare style object
const useStyles = makeStyles((theme) => ({
    root: {
        width: '100%',
    },
    backButton: {
        marginRight: theme.spacing(1),
    },
    instructions: {
        marginTop: theme.spacing(1),
        marginBottom: theme.spacing(1),
    },
}));

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This function will just return an array which consist steps text(steps to import users).
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @method
 * @return {string[]} Steps name - 'Upload File', 'Match Fields', 'Verification', 'Importing Users'
 */
const getSteps = () => {
    return ['Upload File', 'Match Fields', 'Verification', 'Importing Users'];
}


/**
 * @class
 * @component
 *
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This component is developed to import users by uploading an excel sheet(which will follow all required
 * standards).Import process will be complete using 4 steps(upload file, match fields, verification, import).This
 * component will render an horizontal stepper to perform all 4 steps to import users.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Object} props This component received a callback function and event unique ID from parent component.
 * @param {Function} props.callBack Function to navigate steps in import user process.
 * @param {String} props.event_uuid Event's unique ID.
 * @return {JSX.Element}
 */
function HorizontalLabelPositionBelowStepper(props) {
    const classes = useStyles();
    const [activeStep, setActiveStep] = React.useState(0);
    const [step2, setStep2] = useState({});
    const [step3, setStep3] = useState({});
    const [step4, setStep4] = useState({});
    const [matchFieldTempState, setTemp] = useState({});
    const steps = getSteps();

    /**
     * -------------------------------------------------------------------------------------------------------------------
     * @description This function will update state to move on next step(import user process).
     * -------------------------------------------------------------------------------------------------------------------
     *
     * @method
     */
    const handleNext = () => {
        setActiveStep((prevActiveStep) => prevActiveStep + 1);
    };

    /**
     * -------------------------------------------------------------------------------------------------------------------
     * @description This function will update state to move on previous step(import user process).
     * -------------------------------------------------------------------------------------------------------------------
     *
     * @method
     */
    const handleBack = () => {
        setActiveStep((prevActiveStep) => prevActiveStep - 1);
    };

    /**
     * -------------------------------------------------------------------------------------------------------------------
     * @description This function will be used to reset the steps value to 0 in other words it will  land user on first
     * step of import user process.
     * -------------------------------------------------------------------------------------------------------------------
     *
     * @method
     */
    const handleReset = () => {
        setActiveStep(0);
    };

    /**
     * -------------------------------------------------------------------------------------------------------------------
     * @description This function will render final step component which will include newly created users and updated
     * users count on the page with a button to start a fresh import process again from 1st step(import users).
     * -------------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {Object} props Object that contains newly created and updated user's count.
     * @param {Number} props.created Newly added users.
     * @param {Number} props.updated_count Updated users.
     * @return {JSX.Element}
     */
    const FinalStep = (props) => {
        const {created_count, updated_count} = props.data;
        return (
            <div>
                <p>Newly Created users: {created_count}</p>
                <p>Updated users: {updated_count}</p>
                <div className="LastStepBtn">
                    <Button color="primary" variant="contained" onClick={() => {
                        props.resetSteps()
                    }}>
                        Start with a new import
                    </Button>
                </div>
            </div>
        )
    }

    /**
     * -------------------------------------------------------------------------------------------------------------------
     * @description This function will handle rendering of child components for every steps of import users process.This
     * will take current page index(from parameter) and render their relative child component for that step only.
     * -------------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {Number} stepIndex Current step position in import user process.
     * @param {Function} handleNext Function to move next step in import user process.
     * @return {JSX.Element}
     */
    const getStepContent = (stepIndex, handleNext) => {
        switch (stepIndex) {
            case 0:
                return (
                    <FileUpload
                        callBack={(data) => {
                            setStep2(data);
                            setTemp({});
                            handleNext()
                        }}
                        step3CallBack={(data) => {
                            setStep3(data);
                            jumpToThird()
                        }}
                        handleNext={handleNext}
                    />
                );
            case 1:
                return (
                    <MatchField
                        matchFieldTempState={matchFieldTempState}
                        setTemp={setTemp}
                        handleBack={handleBack}
                        handleNext={handleNext}
                        callBack={(data) => {
                            setStep3(data);
                            jumpToThird()
                        }}
                        data={step2}
                    />
                );
            case 2:
                return (
                    <Verification
                        event_uuid={props.event_uuid}
                        props
                        handleBack={handleBackInFieldMatch}
                        handleNext={handleNext}
                        data={step3}
                        callBack={(data) => {
                            setStep4(data)
                        }}
                    />
                );
            case 3:
                return (
                    <FinalStep
                        data={step4}
                        resetSteps={() => {
                            setActiveStep(0);
                            props.callBack && props.callBack();
                        }}
                    />
                );
            default:
                return 'Unknown stepIndex';
        }
    }

    /**
     * -------------------------------------------------------------------------------------------------------------------
     * @description This function will handle the current step state, If in step 3 all match template present then
     * it will set the state of 'back button' to 1 previous step other wise move to 1st step.
     * -------------------------------------------------------------------------------------------------------------------
     *
     * @method
     */
    const handleBackInFieldMatch = () => {
        if (_.has(step3, ['match_template']) && step3.match_template) {
            setActiveStep(0);
        } else {
            handleBack();
        }
    }

    /**
     * -------------------------------------------------------------------------------------------------------------------
     * @description This function update the state(setActiveStep) value to 2 which will take user to jump on 3rd step(
     * import user).
     * -------------------------------------------------------------------------------------------------------------------
     *
     * @method
     */
    const jumpToThird = () => {
        setActiveStep(2);
    }

    return (
        <div className={`${classes.root} importuserModalMain`}>
            <Stepper activeStep={activeStep}>
                {steps.map((label) => (
                    <Step key={label}>
                        <StepLabel>{label}</StepLabel>
                    </Step>
                ))}
            </Stepper>
            <div>
                {activeStep === steps.length ? (
                    <div>
                        <Typography className={classes.instructions}>All steps completed</Typography>
                        <Button onClick={handleReset}>Reset</Button>
                    </div>
                ) : (
                    <div>
                        <div className={classes.instructions}>{getStepContent(activeStep, handleNext)}</div>
                    </div>
                )}
            </div>
        </div>
    );
}

export default HorizontalLabelPositionBelowStepper;