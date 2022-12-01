import React, {useEffect} from 'react'
import NumberInput from '../Common/FormInput/NumberInput'
import SpaceHostSearch from '../Common/FormInput/SpaceHostSearch'
import TextInput from '../Common/FormInput/TextInput'
import SwitchInput from '../Common/FormInput/SwitchInput'
import {Field, reduxForm, change, getFormValues} from 'redux-form'
import useSpaceData from './Containers/SpaceContainer'
import {connect} from 'react-redux'
import {useTranslation} from 'react-i18next';
import SpaceManageHelper from "./SpaceManageHelper";
import eventAction from "../../../redux/action/reduxAction/event";
import Validation from "../../../functions/ReduxFromValidation";
import _ from "lodash";

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description this component is used to render a form to add space using redux-form component.
 * ---------------------------------------------------------------------------------------------------------------------
 * 
 * @param {Object} props Getting current from values from redux-form in real time. 
 * @returns {JSX.Element}
 */
let SpaceManageForm = (props) => {
    // for localization
    const {t} = useTranslation("addSpaceForm");

    const {initialize, handleSubmit} = props;
    let spaceData = useSpaceData(props);

    useEffect(() => {
        initialize(spaceData);
    }, [spaceData]);

    //Condition to check  vip toggle disable for default space EDIT MODE
    let checkDisable = props.spaceEditMode
    && _.has(props, ['spaceFormValues'])
    && _.has(props.spaceFormValues, ['space_is_default'])
    && props.spaceFormValues.space_is_default === 1;
    
    console.log('checkDisable', checkDisable)

    return (
        <div className='addSpacePopupForm'>
            <from onSubmit={handleSubmit}>
                <TextInput
                    name={`space_line_1`}
                    type="text"
                    placeholder={t("line1")}
                    validate={[Validation.required, Validation.max14, Validation.min3]}
                />
                <TextInput
                    name={`space_line_2`}
                    type="text"
                    placeholder={t("line2")}
                    validate={[Validation.max14]}
                />
                <NumberInput
                    name={`space_max_capacity`}
                    type="text"
                    placeholder={t("maxCapacity")}
                    validate={[Validation.required, Validation.maxNumber1000, Validation.minNumber12]}
                />
                <SpaceHostSearch
                    name={`space_host`}
                    placeholder={t("spaceHost")}
                    validate={[Validation.required]}
                />
                <div className="checkboxRow">
                    <h4>{t("vipSpace")}</h4>
                    <SwitchInput
                        name={`space_is_vip`}
                        disabled={checkDisable}
                    />
                </div>
            </from>
        </div>
    )
}

const mapDispatchToProps = (dispatch) => {
    return {
        closeSpacePopup: () => dispatch(eventAction.closeSpacePopup()),
        updateEventForm: (field, value) => dispatch(change('eventManageForm', field, value)),
    }
};

const mapStateToProps = (state) => {
    return {
        eventFormValues: getFormValues('eventManageForm')(state),
        spaceFormValues: getFormValues('createSpaceForm')(state),
        spaceEditMode: state.Event.space_form_status.is_modified,
    }
}
SpaceManageForm = reduxForm({
    form: 'createSpaceForm',
    onSubmit: SpaceManageHelper.handleSpaceFormSubmit,
    keepDirtyOnReinitialize: true,

})(SpaceManageForm)

SpaceManageForm = connect(mapStateToProps, mapDispatchToProps)(SpaceManageForm)
export default SpaceManageForm