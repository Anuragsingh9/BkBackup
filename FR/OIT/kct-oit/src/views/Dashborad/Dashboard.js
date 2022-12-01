import React from 'react';
import SelectField from '../v4/Common/FormInput/SelectField';
import TextInput from '../v4/Common/FormInput/TextInput';
import './Dashboard.css'
import {Field, reduxForm} from 'redux-form';
import NumberInput from '../v4/Common/FormInput/NumberInput';
import TextAreaInput from '../v4/Common/FormInput/TextAreaInput';
import DateInput from '../v4/Common/FormInput/DateInput';
import TimeInput from '../v4/Common/FormInput/TimeInput';
import AutoCompleteInput from '../v4/Common/FormInput/AutoCompleteInput';
import Constants from "../../Constants";
import BreadcrumbsInput from "../v4/Common/Breadcrumbs/BreadcrumbsInput";


/**
 * @class
 * @component
 *
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This is a dashboard component which is currently empty(no information displaying here).When user logged
 * in successfully then he will land on this page first.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @returns {JSX.Element}
 * @constructor
 */
const Dashboard = () => {
    // const handlesubmitM = (e) => {
    //     e.preventDefault();
    //     console.log(e)
    // }
    return (
        <>
            <BreadcrumbsInput
                links={[
                    Constants.breadcrumbsOptions.GROUP_NAME,
                    Constants.breadcrumbsOptions.DASHBOARD,
                ]}
            />
            <div>
                <>
                    <div className="dashContainer"></div>
                    {/* <form onSubmit={handlesubmitM}>
                        <SelectField
                            name={"testing"}
                            value={1}
                            options={[1]}
                            id={"fgh"}
                            // handleChange={handleChangeContentType}
                            disabled={false}
                        />
                        <TextInput />
                        <NumberInput />
                        <TextAreaInput />
                        <DateInput />
                        <TimeInput />
                        <button type='submit'>save</button>
                    </form> 
                    <AutoCompleteInput />*/}
                </>
            </div>
        </>
    )
}
// export default reduxForm({
//     form: "DashboardFrom", // a unique identifier for this form
// })(Dashboard);
export default Dashboard