import Tabs from "react-bootstrap/Tabs";
import Tab from "react-bootstrap/Tab";
import React, {useState} from "react";

const MyNetworkTabs = (props) => {
    const [key, setKey] = useState('recent');
    return (
        <Tabs
            id="controlled-tab-example"
            activeKey={key}
            onSelect={(k) => {
                setKey(k);
                props.handleCurrentTab(k)
            }}
            className="mb-3 mediaDeviceTabs"
        >
            {props?.tabs.map((tab, index) => (
                <Tab tabClassName="mr-5 displayFlex " lassName="mr-3" eventKey={tab.eventKey}
                     title={tab.title}>
                    {tab.component}
                </Tab>
            ))}
        </Tabs>
    );
}

export default MyNetworkTabs;
