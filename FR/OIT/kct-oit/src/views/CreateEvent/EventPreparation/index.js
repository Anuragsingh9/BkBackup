import React, { useEffect, useState } from 'react';
import { makeStyles } from '@material-ui/core/styles';
import Stepper from '@material-ui/core/Stepper';
import Step from '@material-ui/core/Step';
import StepLabel from '@material-ui/core/StepLabel';
import StepContent from '@material-ui/core/StepContent';
import Button from '@material-ui/core/Button';
import Paper from '@material-ui/core/Paper';
import Scheduling from './Scheduling/Scheduling.js';
import ManagingRoles from './ManagingRoles/ManagingRoles.js';
import AddParticipants from './AddParticipants/AddParticipants';
import EventTags from './EventTags/EventTags.js'
import MappingVenue from './MappingVenue/MappingVenue.js'
import Typography from '@material-ui/core/Typography';
import KeyMoments from './KeyMoments/index.js';
import { useSelector, useDispatch } from 'react-redux';

import _ from 'lodash';
// import AddParticipants from '../../AddParticipants/AddParticipants.js';



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

function getSteps(callbk) {
  if (callbk()){  
    
    return ['Scheduling','Key Moments', 'Mapping the Venue', 'Event Tags', 'Managing Participants & Roles'];


  }else{
    return ['Scheduling', 'Mapping the Venue', 'Event Tags', 'Managing Participants & Roles'];

  }
}


/**
 * --------------------------------------------------------------------------------------------------------------------
 * @description This is a container Component for event preparation scheduling step.
 * --------------------------------------------------------------------------------------------------------------------
 *
 * @param {Object} props Route related props to handle page navigation for manage organizer page like history,
 * location,match.
 *
 * @class
 */
const VerticalLinearStepper = (props) => {
  console.log('new', props)
  const classes = useStyles();
  const [activeStep, setActiveStep] = React.useState(0);
  const [accessMode,setMode] = useState(false);
  const eventData = useSelector((data) => data.Auth.eventDetailsData)

  const checkContentEvent = () => {
    var params = props.match.params;
    return (_.has(params,['event_uuid']) && _.has(eventData,['type']) && eventData.type == 2) 
  }
  const steps = getSteps(checkContentEvent);
  
  //useEffect hook for mode change.
  useEffect(()=>{
    if (props.location.pathname && props.location.pathname.includes('access')) {
      setMode(true);
    }
  },[]);

  useEffect(()=>{
    handleReset()
  },[ props.match.params.event_uuid])

 

  const getStepContent = (step) => {
    switch (step) {
      case 0:
        return <Scheduling accessMode={accessMode} handleNext={handleNext} handleBack={handleBack} setShowEventLinks={props.setShowEventLinks} {...props} />;
      case 1:
        return <MappingVenue accessMode={accessMode} handleNext={handleNext} handleBack={handleBack} {...props}/>;
      case 2:
        return <EventTags accessMode={accessMode} handleNext={handleNext} handleBack={handleBack} {...props}/>;
      case 3:
        return <ManagingRoles accessMode={accessMode} handleNext={handleNext} handleBack={handleBack} {...props}/>;
      default:
        return 'Unknown step';
    }
  }

  const getStepContentForContent = (step) => {
    switch (step) {
      case 0:
        return <Scheduling accessMode={accessMode} handleNext={handleNext} handleBack={handleBack} setShowEventLinks={props.setShowEventLinks} {...props} />;

      case 1:
        return <KeyMoments accessMode={accessMode} handleNext={handleNext} handleBack={handleBack}  isAutoCreated = {props.isAutoCreated} {...props} />;
      case 2:
      return <MappingVenue accessMode={accessMode} handleNext={handleNext} handleBack={handleBack} {...props}/>;
      case 3:
        return <EventTags accessMode={accessMode} handleNext={handleNext} handleBack={handleBack} {...props}/>;
      case 4:
        return <ManagingRoles accessMode={accessMode} handleNext={handleNext} handleBack={handleBack} {...props}/>;
      default:
        return 'Unknown step';
    }
  }

  const handleNext = () => {
    setActiveStep((prevActiveStep) => prevActiveStep + 1);
  };

  const handleBack = () => {
    setActiveStep((prevActiveStep) => prevActiveStep - 1);
  };

  const handleReset = () => {
    setActiveStep(0);
  };

  const handleStepChange = (key) => {
    var params = props.match.params;

    if (_.has(params,['event_uuid'])){
      setActiveStep(key)
    }
  }

  return (
    <div className={classes.root}>
      <Stepper activeStep={activeStep} orientation="vertical">
        {steps.map((label, index) => (
          <Step key={label} >
            <StepLabel onClick={()=>{handleStepChange(index)}}>{label}</StepLabel >
            <StepContent>
              <Typography>{checkContentEvent()? getStepContentForContent(index) :getStepContent(index)}</Typography>
              <div className={classes.actionsContainer}>
                <div>
                  {/* <Button
                    disabled={activeStep === 0}
                    onClick={handleBack}
                    className={classes.button}
                  >
                    Back
                  </Button>
                  <Button
                    variant="contained"
                    color="primary"
                    onClick={handleNext}
                    className={classes.button}
                  >
                    {activeStep === steps.length - 1 ? 'Finish' : 'Next'}
                  </Button> */}

                  {/* {activeStep === steps.length - 1 &&<Button
                    variant="contained"
                    color="primary"
                    className={classes.button}
                  >
                    Finish
                  </Button>} */}
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
  );
}
export default VerticalLinearStepper;