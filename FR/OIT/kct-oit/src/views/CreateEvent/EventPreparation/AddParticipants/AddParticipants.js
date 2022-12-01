import React from 'react';
import {Select, Grid} from '@material-ui/core';
import MenuItem from '@material-ui/core/MenuItem';
import FormControl from '@material-ui/core/FormControl';
import NativeSelect from '@material-ui/core/NativeSelect';
import {makeStyles} from '@material-ui/core/styles';
import './AddParticipants.css';
import InfoOutlinedIcon from '@material-ui/icons/InfoOutlined';
import {connect} from 'react-redux'
import {Button} from '@material-ui/core';
import AddUser from '../../../UserSettings/AddUser/AddUser';
import ImportUser from '../../../UserSettings/ImportUser/index'
import Event from '../../../../Models/Event'

const useStyles = makeStyles((theme) => ({
    formControl: {
        margin: theme.spacing(1),
        minWidth: 120,
    },
    selectEmpty: {
        marginTop: theme.spacing(2),
    },
}));

/**
 * @class
 * @component
 *
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This is rendering add participants components  used in Event preparation page.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Object} props Props passed from the parent
 * @param {Event} props.event_data [State] Event data from redux store
 * @return {JSX.Element}
 * @constructor
 */
const AddParticipants = (props) => {
    const {accessMode} = props;
    const classes = useStyles();
    const [state, setState] = React.useState({
        number: '',
        name: 'hai',
    });

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method handles the change in the input values and updates the state
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param {Object} event Javascript event object
     * @method
     */
    const handleChange = (event) => {
        const name = event.target.name;
        setState({
            ...state,
            [name]: event.target.value,
        });
    };

    return (
        <div className="outlineDiv">
            <FormControl className={classes.formControl}>
                <Grid container spacing={3} className="AddParticipantsStepper">
                    <Grid item className="customPara">Create a list of participants from :<InfoOutlinedIcon /></Grid>
                    <Grid item>
                        <Select
                            native
                            value={state.number}
                            disabled={accessMode}
                            onChange={handleChange}
                            name="number"
                            variant="outlined"
                            className={classes.selectEmpty}
                            inputProps={{'aria-label': 'age'}}
                        >
                            <option value="">Please Choose</option>
                            <option value={1}>Manually Add User</option>
                            <option value={2}>Import New User</option>
                            <option value={3} disabled>Group Users</option>
                            <option value={4} disabled>Previous Event Users</option>
                            <option value={5} disabled>Organization Users</option>
                        </Select>
                    </Grid>
                </Grid>
            </FormControl>

            <div>
                {state.number === '1' && <div>
                    <AddUser event_uuid={props.event_data.event_uuid} />
                </div>}
                {state.number === '2' && <div>
                    <ImportUser event_uuid={props.event_data.event_uuid} />
                </div>}
            </div>
            <Button
                variant="contained"
                color="primary"
                onClick={props.handleNext}
            >
                Next
            </Button>
        </div>
    )
}

const mapDispatchToProps = (dispatch) => {
    return {}
}

const mapStateToProps = (state) => {
    return {
        event_data: state.Auth.eventDetailsData
    }
}

export default connect(mapStateToProps, mapDispatchToProps)(AddParticipants);