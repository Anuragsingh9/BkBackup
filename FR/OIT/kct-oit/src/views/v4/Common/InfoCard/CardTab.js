import React, {useEffect, useState} from 'react';
import Box from '@mui/material/Box';
import Tab from '@mui/material/Tab';
import TabContext from '@mui/lab/TabContext';
import TabList from '@mui/lab/TabList';
import TabPanel from '@mui/lab/TabPanel';
import InfoCard from './InfoCard';
import _ from "lodash"

const CardTab = (props) => {
    const [value, setValue] = React.useState(0);

    const handleChange = (event, newValue) => {
        setValue(newValue);
    };
    const renderTab = _.has(props, ['tabs']) && props.tabs !== null && props.tabs !== undefined;

    return (

        <>
            <TabContext value={value} className="p_0">
                <Box className='p_0 flex_center_row'>
                    {
                    _.has(props, ['tabHeading']) 
                    && <p className='cardtabHeading'>{props.tabHeading}</p>
                    }
                    <TabList
                        onChange={handleChange}
                        className='p_0'
                        variant="scrollable"
                        scrollButtons="auto"
                        aria-label="scrollable auto tabs example"
                    >
                        {
                            renderTab && props.tabs.map((tab, index) => (
                                <Tab
                                    label={tab.label}
                                    value={index}
                                    onClick={() => {
                                        if (_.has(props, ["setRecUuid"])) {
                                            props.setRecUuid(tab.rec_uuid)
                                        }
                                    }}

                                />
                            ))
                        }
                    </TabList>
                    {
                         _.has(props, ['gradeFilter']) 
                         && props.gradeFilter
                    }
                </Box>
                {
                    renderTab && props.tabs.map((tab, index) => (
                        <TabPanel className='p_0' value={index} >
                            {tab.component}
                        </TabPanel>
                    ))
                }

            </TabContext>
        </>
    );
}

export default CardTab