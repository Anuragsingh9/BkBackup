import * as React from 'react';
import PropTypes from 'prop-types';
import {useAutocomplete} from '@mui/core/AutocompleteUnstyled';
import CheckIcon from '@mui/icons-material/Check';
import CloseIcon from '@mui/icons-material/Close';
import {styled} from '@mui/material/styles';
import userAction from '../../../../../../redux/action/apiAction/user';
import {connect} from 'react-redux';
import Helper from '../../../../../../Helper';
import _ from 'lodash';
import './MultiSelectUse.css'
import {useDispatch, useSelector} from 'react-redux'
import {useTranslation} from 'react-i18next';

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This method is used for mui styled component
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @method
 */
const Root = styled('div')(
    ({theme}) => `
  color: ${
        theme.palette.mode === 'dark' ? 'rgba(255,255,255,0.65)' : 'rgba(0,0,0,.85)'
    };
  font-size: 14px;
`,
);
//for labels styeld
const Label = styled('label')`
  padding: 0 0 4px;
  line-height: 1.5;
  display: block;
`;

//for input wrapper
const InputWrapper = styled('div')(
    ({theme}) => `
  border: 1px solid ${theme.palette.mode === 'dark' ? '#434343' : '#d9d9d9'};
  background-color: ${theme.palette.mode === 'dark' ? '#141414' : '#fff'};
  border-radius: 4px;
  padding: 1px;
  display: flex;
  flex-wrap: wrap;

  &:hover {
    border-color: ${theme.palette.mode === 'dark' ? '#177ddc' : '#40a9ff'};
  }

  &.focused {
    border-color: ${theme.palette.mode === 'dark' ? '#177ddc' : '#40a9ff'};
    box-shadow: 0 0 0 2px rgba(24, 144, 255, 0.2);
  }

  & input {
    background-color: ${theme.palette.mode === 'dark' ? '#141414' : '#fff'};
    color: ${
        theme.palette.mode === 'dark' ? 'rgba(255,255,255,0.65)' : 'rgba(0,0,0,.85)'
    };
    height: 30px;
    box-sizing: border-box;
    padding: 4px 6px;
    width: 0;
    min-width: 30px;
    flex-grow: 1;
    border: 0;
    margin: 0;
    outline: 0;
  }
`,
);

/**
 * @functional
 * @component
 *
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This component shows the labels and have a delete functionality
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Object} props passed from parent component
 * @param {String} props.label Label text
 * @param {Function} props.onDelete Delete function
 * @return {JSX.Element}
 * @constructor
 */
function Tag(props) {
    const {label, onDelete, ...other} = props;
    return (
        <div {...other}>
            <span>{label}</span>
            <CloseIcon onClick={onDelete} />
        </div>
    );
}

// for prototype of tag
Tag.propTypes = {
    label: PropTypes.string.isRequired,
    onDelete: PropTypes.func.isRequired,
};

// for tag styles
const StyledTag = styled(Tag)(
    ({theme}) => `
  display: flex;
  align-items: center;
  height: 24px;
  margin: 2px;
  line-height: 22px;
  background-color: ${
        theme.palette.mode === 'dark' ? 'rgba(255,255,255,0.08)' : '#fafafa'
    };
  border: 1px solid ${theme.palette.mode === 'dark' ? '#303030' : '#e8e8e8'};
  border-radius: 2px;
  box-sizing: content-box;
  padding: 0 4px 0 10px;
  outline: 0;
  overflow: hidden;

  &:focus {
    border-color: ${theme.palette.mode === 'dark' ? '#177ddc' : '#40a9ff'};
    background-color: ${theme.palette.mode === 'dark' ? '#003b57' : '#e6f7ff'};
  }

  & span {
    overflow: hidden;
    white-space: nowrap;
    text-overflow: ellipsis;
  }

  & svg {
    font-size: 12px;
    cursor: pointer;
    padding: 4px;
  }
`,
);

// for styling the listing the content
const Listbox = styled('ul')(
    ({theme}) => `
  margin: 2px 0 0;
  padding: 0;
  position: absolute;
  list-style: none;
  background-color: ${theme.palette.mode === 'dark' ? '#141414' : '#fff'};
  overflow: auto;
  max-height: 250px;
  border-radius: 4px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
  z-index: 1;

  & li {
    padding: 5px 12px;
    display: flex;

    & span {
      flex-grow: 1;
    }

    & svg {
      color: transparent;
    }
  }

  & li[aria-selected='true'] {
    background-color: ${theme.palette.mode === 'dark' ? '#2b2b2b' : '#fafafa'};
    font-weight: 600;

    & svg {
      color: #1890ff;
    }
  }

  & li[data-focus='true'] {
    background-color: ${theme.palette.mode === 'dark' ? '#003b57' : '#e6f7ff'};
    cursor: pointer;

    & svg {
      color: currentColor;
    }
  }
`,
);

/**
 * @functional
 * @component
 *
 * ---------------------------------------------------------------------------------------------------------------------
 * @description CustomizedHook component is used for select type input component and that is customized according to
 * functionality
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Object} props Props passed from parent component
 * @param {Function} props.userSearch To search a user in an event
 * @param {Event} props.event_data Event related data
 * @param {Event} props.onChange To update the value of input box
 * @returns {JSX.Element}
 * @constructor
 */
function CustomizedHook(props) {
    const [options, setOptions] = React.useState([]);
    const [inputVal, setVal] = React.useState('');
    const [values, setValues] = React.useState([]);
    // for localization
    const {t} = useTranslation(["mapping", "notification", "eventList", "confirm", "labels"]);

    /**
     * ---------------------------------------------------------------------------------------------------------------------
     * @description This method handles the input change and updates the state for inputs
     * ---------------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {Object} eJavascript event object
     */
    const onChange = (e) => {
        // console.log("alert");
        setVal(e.target.value);
        selectChange(e.target.value);
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method is used for fetch the api for matching input string
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param {String} data Entered value by user
     */
    const selectChange = (data) => {
        if (data && data.length >= 3) {
            try {
                props.userSearch({
                    key: data,
                    event_uuid: props.event_data.event_uuid,
                    mode: 'simple',
                    filter: ''
                }).then((res) => {
                    setOptions(res.data.data);
                }).catch((err) => {
                    alert.show(Helper.handleError(err), {type: 'error'});
                })
            } catch (err) {
                alert.show(Helper.handleError(err), {type: 'error'});
            }
        } else {
            setOptions([]);
        }
    }

    const {
        getRootProps,
        getInputLabelProps,
        getInputProps,
        getTagProps,
        getListboxProps,
        getOptionProps,
        groupedOptions,
        value,
        focused,
        setAnchorEl,
    } = useAutocomplete({
        id: 'customized-hook-demo',
        multiple: true,
        options: options,
        value: values,
        placeholder: props.placeholder,
        getOptionLabel: (option) => option.fname,
        onChange: (e, val) => {
            if (e.keyCode === 8) {
                // e.preventDefault()
            } else {
                props.onChange(val)
                setValues(val)
                setVal('');
            }
        }
    });

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method is used for set values for speaker
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     */
    React.useEffect(() => {
        if (props.selectedSpeakers && !_.isEmpty(props.selectedSpeakers)) {
            setValues(props.selectedSpeakers);
            setOptions(props.selectedSpeakers);
        }
    }, [props.selectedSpeakers])

    // getting value of eventRoleLabels  from redux stores state
    const eventRoleLabels = useSelector(data => data.Auth.eventRoleLabels.labels)
    // getting value of eventRolelabelCustomized  from redux stores state
    const eventRolelabelCustomized = useSelector(data => data.Auth.eventRoleLabels.label_customized);
    return (
        <Root>
            <div {...getRootProps()} className="absoluteLabelDiv">
                <Label {...getInputLabelProps()} className="labelAbsolute customPara">
                    {eventRolelabelCustomized == 1 ? Helper.getLabel("speaker", eventRoleLabels) : t("labels:Speaker")}:</Label>
                <InputWrapper ref={setAnchorEl} className={focused ? 'focused' : ''}>
                    {value.map((option, index) => (
                        <StyledTag label={option.fname} {...getTagProps({index})} />
                    ))}
                    <input {...getInputProps()} value={inputVal} onInput={onChange} placeholder={props.placeholder}
                           className="FullWidthInput" />
                </InputWrapper>
            </div>
            {groupedOptions.length > 0 ? (
                <Listbox {...getListboxProps()}>
                    {groupedOptions.map((option, index) => (
                        <li {...getOptionProps({option, index})}>
                            <span>{option.fname}({option.email})</span>
                            <CheckIcon fontSize="small" />
                        </li>
                    ))}
                </Listbox>
            ) : null}
        </Root>
    );
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

export default connect(mapStateToProps, mapDispatchToProps)(CustomizedHook);

// Top 100 films as rated by IMDb users. http://www.imdb.com/chart/top
const top100Films = [
    {fname: 'The Shawshank Redemption', lname: 'demo', email: 'abcd@email.com', id: 1994},
    {fname: 'The Shawshank', lname: 'demo', email: 'abcd@email.com', id: 12},
];