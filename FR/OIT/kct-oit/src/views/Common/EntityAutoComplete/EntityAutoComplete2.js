import React, {useEffect, useState} from 'react';
import TextField from '@material-ui/core/TextField';
import Autocomplete, {createFilterOptions} from '@material-ui/lab/Autocomplete';
import Helper from '../../../Helper'
import {connect} from 'react-redux';
import userAction from '../../../redux/action/apiAction/user';
import {useAlert} from 'react-alert';
import _ from 'lodash';

const filter = createFilterOptions();

/**
 * @global
 * @component
 *
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This is a common component used for searching and fetching entity(Company and Union) data by
 * their name and type.
 * <br>
 * <br>
 * 1.The different types of entities are:- 1. Company 2. Union
 * 2.The search API triggers only when the entered key is greater than 2 characters.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Object} props Props passed from parent component
 * @param {Function} props.entitySearch Function is used to search the entity
 * @param {Object} props.name Name of entity
 * @param {Function} props.selectName Function is used to selected entity
 * @param {Number} props.type Type of entity
 * @returns {JSX.Element}
 * @constructor
 */
const EntityAutocomplete2 = (props) => {
    const [val, setVal] = useState('')
    const [type, setType] = useState()
    const [selectComp, setSelectComp] = useState([])
    const alert = useAlert();

    useEffect(() => {
        if (props.name && !_.isEmpty(props.name)) {
            const company = props.name;
            if (company) {
                setSelectComp([{long_name: company.long_name ? company.long_name : ''}])
                setVal({long_name: company.long_name ? company.long_name : ''})
            }
        }
        setType(props.type);
    }, [])

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method is responsible for handling the changes in the input. It will be called whenever user
     * changes the input value.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {Object} e Javascript event object
     * @param {String} value Entered value of input box
     */
    const handleChange = (e, value) => {
        if (!_.isEmpty(value)) {
            selectChange(value)
        }
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method takes the user input string and calls the entity search API and fetches the data for
     * response. It also updates the state(setSelectComp) from response.
     * <br>
     * <br>
     * The entity search API will be called only when user input is greater than 2.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {String} data Entity data
     */
    const selectChange = (data) => {
        // data = encodeURIComponent(data)
        if (data && data.length >= 3) {
            if (data != undefined && type != undefined) {
                try {
                    props.entitySearch({key: data, type: type}).then((res) => {
                        const data = res.data.data
                        if (data instanceof Array) {
                            setSelectComp(res.data.data);
                        } else {
                            setSelectComp([]);
                        }
                    }).catch((err) => {
                        alert.show(Helper.handleError(err), {type: 'error'});
                    })
                } catch (err) {
                    alert.show(Helper.handleError(err), {type: 'error'});
                }
            } else {
                setSelectComp([]);
            }
        }
    }
    return (
        <Autocomplete
            style={{width: 316}}
            classes={{option: props.classes}}
            value={val}
            autoHighlight
            options={selectComp ? selectComp : ''}
            getOptionLabel={(option) =>
                //  option ? `${option.long_name}` :''
            {
                if (option) {
                    return `${option.long_name}`
                }
                // Add "xxx" option created dynamically
                if (option.long_name) {
                    return option.long_name
                }
            }
            }
            onInputChange={handleChange}
            onChange={(event, newValue) => {
                if (typeof newValue === 'string') {
                    setVal({long_name: newValue.long_name})
                    props.selectName(newValue.long_name)
                    props.CompanyId(newValue.id)
                } else if (newValue && newValue.inputValue) {
                    // Create a new value from the user 
                    setVal({long_name: newValue.inputValue})
                    props.selectName(newValue.inputValue)
                    props.CompanyId(newValue.id)
                } else {
                    if (newValue) {
                        setVal(newValue);
                        props.selectName(newValue.long_name)
                        props.CompanyId(newValue.id)
                    }
                }
            }}
            filterOptions={(options, params) => {
                const filtered = filter(options, params);
                const {inputValue} = params;
                // Suggest the creation of a new value
                const isExisting = options.some((option) => inputValue === option.long_name);
                if (inputValue !== '' && !isExisting) {
                    filtered.push({
                        // long_name: inputValue,
                        long_name: `Add new ${type == 2 ? "Union" : "Company"} "${inputValue}"`,
                        inputValue,
                    });
                }
                return filtered;
            }}
            renderOption={(option) => (
                <React.Fragment>

                    {option.long_name}
                </React.Fragment>
            )}
            renderInput={(params) => (
                <TextField
                    {...params}
                    label={props.type ? props.type === 1 ? "Company" : "Union" : ""}
                    variant="outlined"
                    className="AutoCompleteIput"
                    size="small"
                    margin="dense"
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
        entitySearch: (data) => dispatch(userAction.entitySearch(data)),
    }
}

const mapStateToProps = (state) => {
    return {}
}

export default connect(mapStateToProps, mapDispatchToProps)(EntityAutocomplete2);
