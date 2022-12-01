import {Select} from "@mui/material";
import MenuItem from "@material-ui/core/MenuItem";
import Helper from "../../../Helper";
import useAnalyticsGroupData from "./AnalyticsContainer/AnalyticsContainer";
import {Badge, Checkbox} from "@material-ui/core";
import React, {useState, useEffect} from "react";
import groupAction from "../../../redux/action/reduxAction/group";
import {connect} from "react-redux";
import "./Analytics.css"
import ErrorOutlineIcon from "@mui/icons-material/ErrorOutline";
import Tooltip from "@material-ui/core/Tooltip";


let GroupDropDown = (props) => {
    const groupsData = useAnalyticsGroupData();

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method is used to update the groups data
     * 1. Add the isChecked key to the group drop down so its identify the group checked or not
     * 2.Also give the selected group count
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {GroupObj} value group data
     * @param {String} groupKey Unique key for each group
     */
    const updateCheckedValue = (value, groupKey) => {
        let data = groupsData.get;
        data[value].isChecked = !data[value].isChecked;
        if (!data[value].isChecked) {
            props.setAnalyticGroupDropdown(props.fetch_analytic_group_dropdown.filter(function (e) {return e !== data[value].group_key}))
        } else {
            props.setAnalyticGroupDropdown([...props.fetch_analytic_group_dropdown, groupKey])
        }
        groupsData.set(data);
    }

    return (
        <div className="analyticsGroupDropdown">
            <Tooltip arrow title={groupsData.get.filter(g => g.isChecked).map(g => g.group_name).join(',')} placement="top-start">
            <Select
                id="demo-multiple-name"
                multiple
                displayEmpty
                className="EventlistIcoDrop header__dropdownButton"
                value={groupsData.get.filter(g => g.isChecked)}
                renderValue={(selected) => {
                    return (<div className="flexDiv_middle_xy">
                        <span>GROUPS</span>
                        &nbsp;&nbsp;&nbsp;&nbsp;

                        {selected?.length > 0 ?
                            <Badge badgeContent={selected?.length} color="secondary" /> : ''
                        }

                    </div>);
                }}
            >
                {groupsData.get.map((group, i) => {
                    return <MenuItem value={group.id}
                    >
                        <Checkbox
                            checked={group.isChecked}
                            onChange={(e) => updateCheckedValue(i, group.group_key)}
                        />
                        {`${group.group_name}- [${Helper.groupTypeCapital(group.group_type)}]`}

                    </MenuItem>
                }
                )}
            </Select>
            </Tooltip>
        </div>
    )

}

const mapStateToProps = (state) => {
    return {
        fetch_analytic_group_dropdown: state.Group.analytic_group_dropdown,
    };
};

const mapDispatchToProps = (dispatch) => {
    return {
        setAnalyticGroupDropdown: (dropdowns) => dispatch(groupAction.updateAnalyticGroupDropdown(dropdowns)),
    }
}

GroupDropDown = connect(mapStateToProps, mapDispatchToProps)(GroupDropDown);

export default GroupDropDown;