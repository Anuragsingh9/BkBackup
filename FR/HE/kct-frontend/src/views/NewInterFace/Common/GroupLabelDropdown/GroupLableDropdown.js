import React, { useState } from "react";
import { NavLink } from "react-router-dom";
import { KCTLocales } from "../../../../localization";
import Helper from "../../../../Helper.js";
import Svg from "../../../../Svg";
import "./GroupLableDropdown.css"
import _ from "lodash";
import { useTranslation } from "react-i18next";

/**
 * @class
 * @component
 *
 * ---------------------------------------------------------------------------------------------------------------------
 * @description Component for rendering dropdown inside header component
 * And used for navigation inside the interface
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Object} props Inherited from parent component
 * @param {Object} props.setSelectedKey State for set the selected key
 * @param {Object} props.list List of group name
 * @returns {JSX.Element}
 * @constructor
 */
const GroupLableDropdown = (props) => {
  const { t } = useTranslation("headerDropDown");
  const { activeEventId, event_badge } = props;

  const [open, setOpen] = React.useState(false);
  const anchorRef = React.useRef(null);
  const [selectedName, setSeletedName] = useState("All");

  const onChangeOption = (e, value) => {
    setSeletedName(value.group_name);
    props.setSelectedKey &&
      props.setSelectedKey(value.group_key && value.group_key);
    setOpen(false);
  };

  return (
    <div className="dropdown">
      <a
        className="drop-btn dropdown-toggle customDropFlex pl-0"
        href="#"
        role="button"
        id="dropdownMenuLink"
        data-toggle="dropdown"
        aria-haspopup="true"
        aria-expanded="false"
      >
        <div dangerouslySetInnerHTML={{ __html: Svg.ICON.event_list_filter_icon }}></div>
        <p>{selectedName}</p>
        <span className="fa fa-chevron-down"></span>
      </a>

      <div className="dropdown-menu" aria-labelledby="dropdownMenuLink">
        {/* <NavLink className="dropdown-item" to={activeEventId?`/dashboard/${activeEventId}`:"/event-list"}>{t("My Active Event")}
                </NavLink>
               
                <NavLink className="dropdown-item" to="/event-list">{t("My event registrations")} 
                </NavLink>
                <NavLink className="dropdown-item" to="/change-password">{t("Change Password")} 
                </NavLink>
                <div  className="dropdown-item" onClick={props.logout}>{t("Logout")}
                </div> */}

        <ul
          autoFocusItem={open}
          id="menu-list-grow"
          className="customMenuListDropDown"
        >
          <li
            onClick={(e) =>
              onChangeOption(e, { group_key: "", group_name: "All" })
            }
          >
            All
          </li>
          {props.list && props.list.map((value, i) => (
            <li
              key={value.group_key}
              value={value}
              onClick={(e) => onChangeOption(e, value)}
            >
              {value.group_name}
            </li>
          ))}
          {/* <MenuItem >Profile</MenuItem>
                    <MenuItem >Change password</MenuItem>
                    <MenuItem >Logout</MenuItem> */}
        </ul>
      </div>
    </div>
  );
};

export default GroupLableDropdown;

let meta = {
  groups: [
    {
      group_key: "default",
      group_name: "default",
    },
    {
      group_key: "newgroup",
      group_name: "newgroup",
    },
    {
      group_key: "fifth",
      group_name: "fifth",
    },
    {
      group_key: "fi0007",
      group_name: "fi0007",
    },
    {
      group_key: "fi0008",
      group_name: "fi0008",
    },
    {
      group_key: "FI0027",
      group_name: "FI0027",
    },
  ],
};
