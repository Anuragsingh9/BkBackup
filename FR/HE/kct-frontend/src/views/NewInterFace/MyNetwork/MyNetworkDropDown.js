import {Dropdown, DropdownButton} from "react-bootstrap";
import {useTranslation} from "react-i18next";
import React, {useEffect, useState} from "react";
import MyNetworkTabs from "./MyNetworkTabs";
import Recents from "./Recents/Recents";
import MyList from "./Recents/MyList";

let MyNetworkDropDown = (props) => {
    const {t} = useTranslation('myNetwork');
    const [currentTab,setCurrentTab] = useState('recent');


    const myNetworkData = [
        {
            avatar:"https://s3.eu-west-2.amazonaws.com/kct-dev/testingdev.seque.in/users/avatar/3UpN62bVzdskuT8MCmz6TPa5wVnsiyZCMIweWCP8.png",
            callData: {
                name:"Anurag Singhh",
                position:"BK Dev",
                company: "Pebibits",
                callType: `<i className="fa fa-phone"></i>`,
                callTime: "12 mins ago"
            }

        },
        {
            avatar:"https://s3.eu-west-2.amazonaws.com/kct-dev/testingdev.seque.in/users/avatar/3UpN62bVzdskuT8MCmz6TPa5wVnsiyZCMIweWCP8.png",
            callData: {
                name:"Gourav Verma",
                position:"Full Stack Dev",
                company: "Pebibits",
                callType: `<i className="fa fa-phone"></i>`,
                callTime: "9 mins ago"
            }
        },
        {
            avatar:"https://s3.eu-west-2.amazonaws.com/kct-dev/testingdev.seque.in/users/avatar/3UpN62bVzdskuT8MCmz6TPa5wVnsiyZCMIweWCP8.png",
            callData: {
                name:"Abhishek Vyas",
                position:"QA Team",
                company: "Pebibits",
                callType: `<i className="fa fa-phone"></i>`,
                callTime: "29 mins ago"
            }
        },
        {
            avatar:"https://s3.eu-west-2.amazonaws.com/kct-dev/testingdev.seque.in/users/avatar/3UpN62bVzdskuT8MCmz6TPa5wVnsiyZCMIweWCP8.png",
            callData: {
                name:"Anurag Singhh",
                position:"BK Dev",
                company: "Pebibits",
                callType: `<i className="fa fa-phone"></i>`,
                callTime: "12 mins ago"
            }

        },
        {
            avatar:"https://s3.eu-west-2.amazonaws.com/kct-dev/testingdev.seque.in/users/avatar/3UpN62bVzdskuT8MCmz6TPa5wVnsiyZCMIweWCP8.png",
            callData: {
                name:"Gourav Verma",
                position:"Full Stack Dev",
                company: "Pebibits",
                callType: `<i className="fa fa-phone"></i>`,
                callTime: "9 mins ago"
            }
        },
        // {
        //     avatar:"https://s3.eu-west-2.amazonaws.com/kct-dev/testingdev.seque.in/users/avatar/3UpN62bVzdskuT8MCmz6TPa5wVnsiyZCMIweWCP8.png",
        //     callData: {
        //         name:"Anurag Singhh",
        //         callType: `<i className="fa fa-phone"></i>`,
        //         callTime: "12 mins ago"
        //     }
        //
        // },
        // {
        //     avatar:"https://s3.eu-west-2.amazonaws.com/kct-dev/testingdev.seque.in/users/avatar/3UpN62bVzdskuT8MCmz6TPa5wVnsiyZCMIweWCP8.png",
        //     callData: {
        //         name:"Gourav Verma",
        //         callType: `<i className="fa fa-phone"></i>`,
        //         callTime: "9 mins ago"
        //     }
        // },
    ]
    const handleTabChange = (value) => {
        setCurrentTab(value);
    }

    const componentTabs = [
        {title: "Recents", eventKey: 'recent',   component: <Recents data={myNetworkData} currentTabValue= {currentTab}/> },
        {title: "My list", eventKey: 'my_list', component: <MyList data={myNetworkData} currentTabValue= {currentTab} /> }
    ]

    return (
        <div className="mb-2">
                <DropdownButton
                    key='up'
                    id={`dropdown-button-drop-up`}
                    drop='up'
                    variant="secondary"
                    autoClose={false}
                    title={t("My Network")}
                >
                    <Dropdown.Item eventKey="recent" >{t("My Network")} </Dropdown.Item>
                    <div className="modal-content margin-l20 my-network">
                        <MyNetworkTabs tabs={componentTabs} handleCurrentTab={handleTabChange} />
                    </div>
                </DropdownButton>
        </div>
    )
}



export default MyNetworkDropDown;