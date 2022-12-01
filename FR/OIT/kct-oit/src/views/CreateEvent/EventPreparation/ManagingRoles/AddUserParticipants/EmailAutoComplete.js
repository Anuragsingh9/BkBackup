import React, {useEffect, useState} from "react";
import TextField from "@material-ui/core/TextField";
import Autocomplete, {createFilterOptions,} from "@material-ui/lab/Autocomplete";
import Helper from "../../../../../Helper";
import {connect} from "react-redux";
import userAction from "../../../../../redux/action/apiAction/user";
import {useAlert} from "react-alert";
import _ from "lodash";
import '../../../../UserSettings/AddUser/EmailAutoComplete.css';

const filter = createFilterOptions();

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description Component for rendering user autocomplete select inputs.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Object} props Props passed from the parent component
 * @param {Event} props.event_data Event related data
 * @param {User} props.user_data User related data
 * @param {Function} props.userSearch Method to search user's data by entering user's email in input box
 * @class
 * @component
 */
const EmailAutocomplete = (props) => {
    const {input, ...rest} = props;
    const [val, setVal] = useState("");
    const [selectUser, setSelectUser] = useState([]);
    const alert = useAlert();

    //useEffect hook is used for getting props value from parent component when component mount

    useEffect(() => {


        if (!_.isEmpty(props.name)) {
            const spaceHost = props.name;
            // setSelectUser([{ id : spaceHost.id ?spaceHost.id:'',
            //  fname: spaceHost.fname ? spaceHost.fname:'',
            // lname: spaceHost.lname ?spaceHost.lname:''  ,
            //  email : spaceHost.email ? spaceHost.email:'' }])
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


    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method is used for handling changes in input box
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {String} value Enter key or value by the user in the input box to search user
     */
    const handleChange = _.debounce((e, value) => {
        selectChange(value)
    }, 1000)

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method will fetch users according to the key entered by user in input box
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {String} data Entered key by user in the input box
     */
    const selectChange = (data) => {
        if (data && data.length >= 3) {
            const params = {
                key: data,
                event_uuid: props.event_data.event_uuid,
                mode: 'simple',
                filter: 'space_host',
                groupKey: props.gKey,
            }
            try {
                props
                    .userSearch({
                        key: data,
                        event_uuid: props.event_data.event_uuid,
                        mode: "simple",
                        filter: "space_host",
                        groupKey: props.gKey,
                    })
                    .then((res) => {
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
            options={selectUser ? selectUser : ''}
            classes={{
                option: props.classes,
            }}
            name={props.name}
            value={val}
            autoHighlight
            getOptionLabel={(option) => {

                if (option) {
                    return `${option.email.toLowerCase()}`
                }
                // Add "xxx" option created dynamically
                if (option.email) {
                    return option.email.toLowerCase()
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
                    const data = {email: newValue.inputValue.toLowerCase()}
                    setVal({email: newValue.inputValue.toLowerCase()})
                    props.userData && props.userData(data)
                    // props.CompanyId(newValue.id)
                } else {

                    setVal(newValue);
                    props.userData && props.userData(newValue)
                    // props.selectName(newValue.inputValue)
                    // props.CompanyId(newValue.id)
                }
            }}

            filterOptions={(options, params) => {
                const filtered = filter(options, params);
                const {inputValue} = params;

                // Suggest the creation of a new value
                const isExisting = options.some(
                    (option) => inputValue === option.email
                );

                if (inputValue !== '' && !isExisting) {
                    filtered.push({
                        // long_name: inputValue,
                        email: `Add new email "${inputValue.toLowerCase()}"`,
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
            renderInput={(params) => {
                return (
                    <div>
                        <TextField
                            {...params}
                            // label="Space Host "
                            name={props.input.name}
                            className="autoCompleteInput custom_email_tag convertPlaceholderUppercase"
                            variant="outlined"
                            size="small"
                            value={val}
                            placeholder="Enter Email"
                            disabled={props.disabled ? props.disabled : false}
                            errorText={props.error}
                            error={props.error}
                            inputProps={{
                                ...params.inputProps,
                                autoComplete: 'new-password', // disable autocomplete and autofill
                                style: {textTransform: "lowercase"}
                            }}
                        />
                        {props.touched &&
                        ((props.meta.error && <span className="text-danger">{props.meta.error}</span>) ||
                            (props.meta.warning && <span>{props.meta.warning}</span>))}
                    </div>
                )
            }
            }
        />
    )
}

const mapDispatchToProps = (dispatch) => {
    return {
        userSearch: (data) => dispatch(userAction.userSearch(data)),
    };
};

const mapStateToProps = (state) => {
    return {
        event_data: state.Auth.eventDetailsData,
    };
};
export default connect(mapStateToProps, mapDispatchToProps)(EmailAutocomplete);
