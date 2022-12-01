import React from 'react';
import DragList from './DragList';


/**
 * @class
 * @component
 *
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This component is developed to render static fields  for drag  user experience in step 2 on import user
 * process.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Object} props Props passed from parent component
 * @param {Object} props.data Match field data
 * @param {String} props.data.db_name Database name
 * @param {String} props.data.label Labels(fname,lname,email)
 * @param {Boolean} props.data.required Field required status
 * @param {Function} props.callBack Callback handler on the success
 * @param {Function} props.matchFieldTempState Call back when the temporary data is set
 * @param {Function} props.setTemp To set temporary data
 * @param {Function} props.handleBack To redirect user to previous step
 * @param {Function} props.handleNext To redirect user to next step
 * @return {JSX.Element}
 */
const MatchField = (props) => {
    const {data} = props;
    return (
        <div style={{width: '100%'}}>
            <DragList
                fileName={data.file_name}
                matchFields={data.headings}
                callBack={props.callBack}
                callBackData={props.matchFieldTempState}
                setTemp={props.setTemp}
                staticFields={
                    {
                        personal_tab: [
                            {label: "First Name", db_name: "fname", required: true},
                            {label: "Last Name", db_name: "lname", required: true},
                            {label: "Email", db_name: "email", required: true},
                            {label: "City", db_name: "city", required: false},
                            {label: "Country", db_name: "country", required: false},
                            {label: "Address", db_name: "address", required: false},
                            {label: "Postal", db_name: "postal", required: false},
                            {label: "Phone Number", db_name: "phone_number", required: false},
                            {label: "Mobile Number", db_name: "mobile_number", required: false},
                            {label: "Company", db_name: "company_name", required: false},
                            {label: "Company Position", db_name: "company_position", required: false},
                            {label: "Union", db_name: "union_name", required: false},
                            {label: "Union Position", db_name: "union_position", required: false},
                            {label: "Internal ID", db_name: "internal_id", required: false},
                            // {label: "Gender", db_name: "gender", required: false},
                            // {label: "Grade", db_name: "grade", required: false},
                        ]
                    }
                }
                handleNext={props.handleNext}
                handleBack={props.handleBack}
            />
        </div>
    )
}

export default MatchField;