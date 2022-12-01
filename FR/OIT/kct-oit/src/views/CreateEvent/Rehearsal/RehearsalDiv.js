import React, {useEffect, useState} from 'react';
import eventAction from '../../../redux/action/apiAction/event';
import {useDispatch} from 'react-redux';
import _ from 'lodash';
import {CopyToClipboard} from 'react-copy-to-clipboard';
import {useAlert} from 'react-alert';
import {Button} from '@material-ui/core';
import Helper from '../../../Helper';
import {makeStyles} from '@material-ui/core/styles';
import Stepper from '@material-ui/core/Stepper';
import Step from '@material-ui/core/Step';
import StepLabel from '@material-ui/core/StepLabel';
import StepContent from '@material-ui/core/StepContent';
import Paper from '@material-ui/core/Paper';
import Rehearsal from './index.js';
import Typography from '@material-ui/core/Typography';

const queryString = require('query-string');
const useStyles = makeStyles((theme) => ({
  root: {
    width: '100%',
  },
  button: {
    marginTop: theme.spacing(1),
    marginRight: theme.spacing(1),
  },
  actionsContainer: {
    marginBottom: theme.spacing(2),
  },
  resetContainer: {
    padding: theme.spacing(3),
  },
}));

function getSteps() {
  return ['Access Links'];
}

/**
 * @deprecated
 */
const RehearsalDiv = (props) => {
  const classes = useStyles();
  const [activeStep, setActiveStep] = React.useState(0);
  const steps = getSteps();

  /**
   * -------------------------------------------------------------------------------------------------------------------
   * @description - this method is used for getting current content of steps
   * -------------------------------------------------------------------------------------------------------------------
   *
   * @param {*} step
   * @returns {JSX.Element|string}
   */
  const getStepContent = (step) => {
    switch (step) {
      case 0:
        return <Rehearsal handleNext={handleNext} handleBack={handleBack} {...props} />;

      default:
        return 'Unknown step';
    }
  }

  /**
   * -------------------------------------------------------------------------------------------------------------------
   * @description - this method is used for going to next step
   * -------------------------------------------------------------------------------------------------------------------
   */

  const handleNext = () => {
    setActiveStep((prevActiveStep) => prevActiveStep + 1);
  };

  /**
   * -------------------------------------------------------------------------------------------------------------------
   * @description - this method is used for reset step
   * -------------------------------------------------------------------------------------------------------------------
   */

  const handleBack = () => {
    setActiveStep((prevActiveStep) => prevActiveStep - 1);
  };


  /**
   * -------------------------------------------------------------------------------------------------------------------
   * @description - this method is used handling Step Change
   * -------------------------------------------------------------------------------------------------------------------
   *
   * @param {*} key
   */

  const handleReset = () => {
    setActiveStep(0);
  };

  const handleStepChange = (key) => {
    var params = props.match.params;

    if (_.has(params, ['event_uuid'])) {
      setActiveStep(key)
    }
  }


  // useEffect is used for get links when component render first time

    useEffect(() => {
    var params = props.match.params;
    if (_.has(params, ['event_uuid'])) {
      // getLink(params.event_uuid);
    }
  }, [])


  return (
    <div>
      <div className={classes.root}>
        <Stepper activeStep={activeStep} orientation="vertical">
          {steps.map((label, index) => (
            <Step key={label} >
              <StepLabel onClick={() => {handleStepChange(index)}}>{label}</StepLabel >
              <StepContent>
                <Typography>{getStepContent(index)}</Typography>
                <div className={classes.actionsContainer}>
                  <div>
                  </div>
                </div>
              </StepContent>
            </Step>
          ))}
        </Stepper>
        {activeStep === steps.length && (
          <Paper square elevation={0} className={classes.resetContainer}>
            <Typography>All steps completed - you&apos;re finished</Typography>
            <Button onClick={handleReset} className={classes.button}>
              Reset
            </Button>
          </Paper>
        )}
      </div>
    </div>
  )
}

export default RehearsalDiv;