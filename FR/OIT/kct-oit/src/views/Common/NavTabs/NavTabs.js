import React, {useEffect} from 'react';
import PropTypes from 'prop-types';
import {makeStyles} from '@material-ui/core/styles';
import Tabs from '@material-ui/core/Tabs';
import Tab from '@material-ui/core/Tab';
import Typography from '@material-ui/core/Typography';
import Box from '@material-ui/core/Box';
import './NavTabs.css';
import _ from 'lodash';

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This function is receiving props(from main nav tab components - down below)  to render content box of
 * nav tab's child.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @method
 * @param {Object} props Inherited from parent component
 * @param {JSX} props.children JSX element for tab panel
 * @param {Number} props.value Index of the tab panel
 * @param {Number} props.index Index of tab panel
 * @returns {JSX.Element}
 * @constructor
 */
const TabPanel = (props) => {
    const {children, value, index, ...other} = props;

    return (
        <div
            role="tabpanel"
            hidden={value !== index}
            id={`nav-tabpanel-${index}`}
            aria-labelledby={`nav-tab-${index}`}
            {...other}
        >
            {value === index && (
                <Box p={3}>
                    <Typography>{children}</Typography>
                </Box>
            )}
        </div>
    );
}
// prototype object
TabPanel.propTypes = {
    children: PropTypes.node,
    index: PropTypes.any.isRequired,
    value: PropTypes.any.isRequired,
};

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This function will take index(from parameter) and return an object to set 'ID' and 'aria-controls'
 * attribute.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @method
 * @param {Number} index Index of nav tab
 * @returns {Object} HTML attributes
 */
function a11yProps(index) {
    return {
        id: `nav-tab-${index}`,
        'aria-controls': `nav-tabpanel-${index}`,
    };
}

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This function will take data(for create a basic link component for nav tab) from parameter
 * and return a component(JSX) on which if user clicks then it will render relative child components to it.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @method
 * @param {Object} props Props passed from parent component
 * @param {String} props.aria-controls Css related key for link tab
 * @param {Boolean} props.disabled To disable the link tab
 * @param {Boolean} props.fullWidth To get the full width view
 * @param {String} props.href link's
 * @param {String} props.id User's Id
 * @param {Boolean} props.indicator To indicate the current tab by showing a horizontal line below selected tab
 * @param {String} props.label Label on link tab
 * @param {Function} props.onChange Function is used change the state
 * @param {Boolean} props.selected Link is selected or not
 * @param {String} props.textColor Text color
 * @param {Number} props.value Link value
 * @returns {JSX.Element}
 * @constructor
 */
function LinkTab(props) {
    return (
        <Tab wrapped
            onClick={(e) => {
                e.preventDefault()
            }}
            {...props}
        />
    );
}

//Function which return an object of style(flexGrow, backgroundColor)
const useStyles = makeStyles((theme) => ({
    root: {
        flexGrow: 1,
        backgroundColor: theme.palette.background.paper,
    },
}));


/**
 * @global
 * @component
 *
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This is a common component which takes 'tabData' object(to create nav link + box component for their
 * child prop) and route props(for tab's navigation) to render.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Object} props This component is received route related props eg - history,location,match
 * @return {JSX.Element}
 */
const NavTabs = (props) => {
    const classes = useStyles();
    const [value, setValue] = React.useState(0);
    const {tabData} = props;


    useEffect(() => {
        const thePath = window.location.pathname
        const lastItem = thePath.substring(thePath.lastIndexOf('/') + 1)
        if (lastItem && lastItem === 'technical-setting') {
            setValue(1);
        }
    }, [window.location.pathname])

    /**
     * -------------------------------------------------------------------------------------------------------------------
     * @description This function will take value of tab(from parameter) on which user clicks and then set it to state
     * 'setValue' so that related box component will be render just after click.
     * <br>
     * <br>
     * This function handles change of tab.
     * -------------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {Object} event  Javascript event object
     * @param {Number} newValue To set new value
     * @returns {JSX.Element}
     */
    const handleChange = (event, newValue) => {
        setValue(newValue);
        if (props.redirectValue) {
            props.history.push(props.redirectValue[newValue]);
        }
    };
    return (
        <div className={classes.root}>
            <Tabs
                value={value}
                onChange={handleChange}
                indicatorColor="primary"
                textColor="primary"
                aria-label="nav tabs"
            >
                {tabData.map((item, key) => {
                    return <LinkTab label={item.label} href={item.href} disabled={item.disable} {...a11yProps(key)} />
                })}
            </Tabs>

            {tabData.map((item, key) => {
                return (
                    <TabPanel value={value} index={key}>
                        {item.child}
                    </TabPanel>
                )
            })}

        </div>
    );
}

export default NavTabs