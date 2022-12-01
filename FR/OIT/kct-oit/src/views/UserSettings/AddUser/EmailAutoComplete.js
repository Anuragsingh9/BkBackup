import React, {useEffect, useState} from 'react';
import TextField from '@material-ui/core/TextField';
import Autocomplete, {createFilterOptions} from '@material-ui/lab/Autocomplete';
import {useAlert} from 'react-alert';
import _ from 'lodash';
import {connect} from 'react-redux';
import Helper from '../../../Helper'
import userAction from '../../../redux/action/apiAction/user';
// import './UserAutoComplete.css';

/**
 * @deprecated
 */
const filter = createFilterOptions();

const EmailAutocomplete = (props) => {
    const [val, setVal] = useState('')
    const [selectUser, setSelectUser] = useState([])
    const alert = useAlert();
    //useEffect hook is used for getting props value from parant component when component mount

    useEffect(() => {

        if (!_.isEmpty(props.name)) {
            const spaceHost = props.name;
            setVal({
                id: spaceHost.id ? spaceHost.id : '',
                fname: spaceHost.fname ? spaceHost.fname : '',
                lname: spaceHost.lname ? spaceHost.lname : '',
                email: spaceHost.email ? spaceHost.email : ''
            })

        } else {
            setVal({
                id: '',
                fname: '',
                lname: '',
                email: ''
            })
        }
    }, [props.name])


    const handleChange = (e, value) => {

        selectChange(value)

    }


    const selectChange = (data) => {
        if (data && data.length >= 3) {


            const params = {
                key: data,
                event_uuid: props.event_data.event_uuid,
                mode: 'simple',
                filter: 'space_host'


            }


            try {
                props.userSearch({key: data, event_uuid: props.event_data.event_uuid, mode: 'simple', filter: 'space_host'}).then((res) => {

                    setSelectUser(res.data.data);
                }).catch((err) => {

                    alert.show(Helper.handleError(err), {type: 'error'});
                })
            } catch (err) {
                alert.show(Helper.handleError(err), {type: 'error'});
            }

        } else {
            setSelectUser([]);
        }

    }

    return (
        <Autocomplete

            // style={{ width: 300 }}
            options={selectUser ? selectUser : ''}
            classes={{
                option: props.classes,
            }}
            name={props.name}
            value={val}
            autoHighlight
            getOptionLabel={(option) => {

                if (option) {
                    return `${option.email}`
                }
                // Add "xxx" option created dynamically
                if (option.email) {
                    return option.email
                }
            }


                // option.fname ? `${option.fname} ${option.lname} ${"("}${option.email}${")"} ` : ""
            }
            onInputChange={handleChange}
            // onChange={(event, values) => {
            //     if (values) {
            //         // selectChange(values)
            //         props.id(values.id)
            //         setVal({ fname: values.fname, lname: values.lname, email: values.email })
            //     }
            // }}
            onChange={(event, newValue) => {


                if (newValue && newValue.inputValue) {
                    // Create a new value from the user input
                    const data = {email: newValue.inputValue}
                    setVal({email: newValue.inputValue})
                    props.userData(data)
                    // props.CompanyId(newValue.id)
                } else {

                    setVal(newValue);
                    props.userData(newValue)
                    // props.selectName(newValue.inputValue)
                    // props.CompanyId(newValue.id)
                }
            }}

            filterOptions={(options, params) => {
                const filtered = filter(options, params);
                const {inputValue} = params;

                // Suggest the creation of a new value
                const isExisting = options.some((option) => inputValue === option.email);

                if (inputValue !== '' && !isExisting) {
                    filtered.push({
                        // long_name: inputValue,
                        email: `Add new email "${inputValue}"`,
                        inputValue,
                    });
                }
                return filtered;
            }}

            renderOption={(option) => (
                <React.Fragment>

                    {option.email}
                </React.Fragment>
            )}


            renderInput={(params) => (
                <TextField
                    {...params}
                    // label="Space Host "
                    className="autoCompleteInput"
                    variant="outlined"
                    placeholder="Enter Email"
                    size="small"
                    disabled={props.disabled ? props.disabled : false}
                    errorText={props.errorText}
                    error={props.error}
                    inputProps={{
                        ...params.inputProps,
                        autoComplete: 'new-password', // disable autocomplete and autofill
                    }}
                />
            )}
        />
    )
}

const mapDispatchToProps = (dispatch) => {
    return {

        userSearch: (data) => dispatch(userAction.userSearch(data)),
    }
}

const mapStateToProps = (state) => {
    return {
        event_data: state.Auth.eventDetailsData
    }

}


export default connect(mapStateToProps, mapDispatchToProps)(EmailAutocomplete);
