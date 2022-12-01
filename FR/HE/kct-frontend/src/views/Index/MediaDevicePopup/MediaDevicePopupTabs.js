import React, {useEffect, useState} from 'react';
import Tab from 'react-bootstrap/Tab';
import Tabs from 'react-bootstrap/Tabs';
import _ from 'lodash';


function MediaDevicePopupTabs(props) {
    const [key, setKey] = useState('media');
    const [mediaIcon, setMediaIcon] = useState('');
    const [BGIcon, setBGIcon] = useState('');

    const images = {
        media :'https://s3.eu-west-2.amazonaws.com/kct-dev/hctdevelopment.seque.in/users/avatar/c47bf0cd-7f52-4282-82db-fd947cdfa00c.',
        background_img : 'https://prod-hct-bucket.s3.eu-central-1.amazonaws.com/version.humannconnect.com/event_live_images/501a200c-55e8-11ed-af22-02cf37237d5c/zwky4TJ0VWhZyUSw775FBUCKT6BjBQmx4WomkF4S_thumb.jpg'
    }

    const doesTabExist = _.has(props,['tabs']);
    useEffect(() => {
        if (key === 'media'){
            setBGIcon('')
            setMediaIcon('Media Settings')
        }if (key === 'background_img'){
            setMediaIcon('')
            setBGIcon('Background Effects')
        }
    },[key])

    return (
        <Tabs
            id="controlled-tab-example"
            activeKey={key}
            onSelect={(k) => setKey(k)}
            className="mb-3 mediaDeviceTabs"
        >
            {doesTabExist && props?.tabs.map((tab,index)=>(
                <Tab tabClassName="mr-5 displayFlex " lassName="mr-3" eventKey={tab.eventKey}
                     title={<div className="tab-item">{tab.icon} {tab.eventKey === 'media' ? `${mediaIcon}` : `${BGIcon}`}</div>} >
                    {tab.component}
                </Tab>
            ))}
        </Tabs>
    );
}

export default MediaDevicePopupTabs;