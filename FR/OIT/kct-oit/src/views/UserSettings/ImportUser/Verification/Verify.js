import React, {useState} from 'react';
import _ from 'lodash';
import {makeStyles} from '@material-ui/core/styles';
import {Button} from '@material-ui/core';
import {useDispatch, useSelector} from 'react-redux';
import {useAlert} from 'react-alert';
import Helper from '../../../../Helper';
import userAction from '../../../../redux/action/apiAction/user';
import DataTable from '../../../Common/DataTable';


const useStyles = makeStyles({
    table: {
        minWidth: 650,
    },
});


/**
 * @class
 * @component
 *
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This component is developed for 3rd step of import user process.In this component user can verify the
 * imported users data and selected column(selected from 2nd step-match fields).
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Object} props Props passed from parent for 3rd step of user Import process
 * @param {Array} props.users Array of users present in sheet
 * @param {String} props.event_uuid Event uuid to add users in an event
 * @param {Function} props.handleBack To handle the back button or to take the user to one step back in import process
 * @param {Function} props.handleNext To handle the next button or to take the user to one step forward in import process
 * @return {JSX.Element}
 * @constructor
 */
function BasicTable(props) {
    let usersData = [];
    const rows = props.users.map((item, key) => {
        // usersData.push({
        //     ...item,
        //     gender: item.gender ? item.gender.toLowerCase() : null,
        //     grade: item.grade ? item.grade.toLowerCase() : null,
        // });
        return {...item, id: key + 1};
    });

    const dispatch = useDispatch();
    const alert = useAlert();
    const [columns, setColumn] = useState([]);
    const user_badge = useSelector((state) => state.Auth.userSelfData)
    const classes = useStyles();
    const group = _.has(user_badge, ['current_group']) && user_badge.current_group;
    let data = {};
    // let data = {
    //     user: usersData,
    //     group_key: group.group_key,
    //     group_role: 1,
    // }

    if (props.event_uuid) {
        // data["event_uuid"] = props.event_uuid
        data = {
            user: props.users,
            group_key: group.group_key,
            group_role: 1,
            event_uuid: props.event_uuid
        }
    } else {
        data = {
            user: props.users,
            group_key: group.group_key,
            group_role: 1,
        }
    }

    const column = [
        {
            field: 'fname',
            headerName: 'First Name',
            width: 190,
        },
        {
            field: 'lname',
            headerName: 'Last Name',
            width: 190,
        },
        {
            field: 'company',
            headerName: 'Company',
            width: 150,
        },
        {
            field: 'company_position',
            headerName: 'Company Position',
            width: 140,
        },
        {
            field: 'union',
            headerName: 'Union',
            width: 120,
        },
        {
            field: 'union_position',
            headerName: 'Union Position',
            width: 120,
        },
        {
            field: 'email',
            headerName: 'Email',
            width: 180,
        },
        {
            field: 'city',
            headerName: 'City',
            width: 180,
        },
        {
            field: 'postal',
            headerName: 'Postal',
            width: 180,
        },
        {
            field: 'mobiles',
            headerName: 'Mobile',
            width: 180,
            renderCell: (params) => {
                const {row} = params;
                if (!_.isEmpty(row.mobiles)) {
                    const mobile = row.mobiles[0];

                    return (
                        <span>
                            {mobile.country_code ? `${mobile.country_code}-${mobile.number}` : `${mobile.number}`}
                        </span>
                    )
                }

            }
        },
        {
            field: 'phones',
            headerName: 'Phone',
            width: 180,
            renderCell: (params) => {
                const {row} = params;
                if (!_.isEmpty(row.phones)) {
                    const mobile = row.phones[0];

                    return (
                        <span>
                            {mobile.country_code ? `${mobile.country_code}-${mobile.number}` : `${mobile.number}`}
                        </span>
                    )
                }

            }
        },
        // {
        //     field: 'gender',
        //     headerName: 'Gender',
        //     width: 140,
        // },
        // {
        //     field: 'grade',
        //     headerName: 'Grade',
        //     width: 140,
        // },

    ];

    /**
     * -------------------------------------------------------------------------------------------------------------------
     * @description This function will handle an API call to submit data(imported users list and verified from match field
     * step) and move user to next step in import user process.
     * -------------------------------------------------------------------------------------------------------------------
     *
     * @method
     */
    const handleSubmit = () => {
        try {
            dispatch(userAction.addMultiple(data)).then((res) => {
                alert.show('Succesfully submit');
                props.handleNext();
                props.callBack(res.data.meta);
            }).catch((err) => {
                alert.show(Helper.handleError(err), {type: 'error'});
            })
        } catch (err) {
            alert.show(Helper.handleError(err), {type: 'error'});
        }
    }

    return (
        <div>
            {_.has(props.data, ['match_template'])
                && props.data.match_template
                && <h3>Your Fields Matched perfectly with the required fields.</h3>}

            <DataTable
                disableCheckBox={true}
                columns={column}
                rows={rows}
            />
            <div className="BtnNxt">
                <Button variant="contained" color="primary" onClick={handleSubmit}>
                    Next Step
                </Button>
                <Button onClick={props.handleBack}>Back</Button>
            </div>
        </div>
    );
}

export default BasicTable;