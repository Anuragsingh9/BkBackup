import {React, useState, useEffect} from 'react';
import {TextField, Button, Grid} from '@material-ui/core';
import './EditLabel.css';

/**
 * @class
 * @component
 *
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This component is developed to take input values for new labels(in both language - english/French) and
 * a save button to update label.This component will render when user clicks on 'modify' button under labels section
 * in design setting page.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Object} props Inherited from parent component
 * @param {Function} props.getData To get the label data
 * @param {Number} props.index Index of label
 * @param {Object} props.imageSection Use for show image section
 * @param {Array} props.locales Array of locales
 * @param {Function} props.update Function is used to update labels
 * @returns {JSX.Element}
 * @constructor
 */
const EditLabel = (props) => {
    const [labelButton, setLabelButton] = useState('Modify');
    const [showBox, setShowBox] = useState(false)
    const [values, setValues] = useState([]);

    useEffect(() => {
        setValues(props.locales)
    }, [props.locales])


    const editLabels = () => {
        if (showBox === false) {
            setShowBox(true);
            setLabelButton('Save');
        } else {
            setShowBox(false);
            setLabelButton('Modify');
        }

        if (labelButton == "Save") {
            const data = {
                index: props.index,
                value: values
            }
            props.getData(data)
        }
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This function will trigger when edit label form(to take new labels to change) is opened and user
     * click on 'cancel' button.This function will close the edit label form component and all unsaved changes will be
     * lost.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     */
    const onCancelHandle = () => {
        setShowBox(false);
        setLabelButton('Modify');
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method will when user input any value in the edit label form component.This will take all input
     * values from its parameter and then save them in a state(setValues) to call update label API.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {Number} i Number's of label
     * @returns {Function}
     */
    const handleChange = i => e => {
        let newArr = [...values];
        newArr[i].value = e.target.value
        setValues(newArr);
    }

    return (
        <div>
            <Grid container lg={12} className="LabelEditWrap">
                {showBox &&
                values && values.map((v, i) => (
                    <Grid item className="labelContentDiv">
                        {v.locale == 'en' && <div key={i}>
                            <label htmlFor="EN_Label">{v.locale.toUpperCase()} </label>
                            <TextField
                                id="EN_Label"
                                placeholder="Label"
                                variant="outlined"
                                size="small"
                                onChange={handleChange(i)}
                                value={v.value}
                                disabled={props?.disabled}
                            />
                        </div>}
                        {v.locale == 'fr' && <div>
                            <label htmlFor="FR_Label">{v.locale.toUpperCase()} </label>
                            <TextField
                                id="FR_Label"
                                placeholder="Label"
                                variant="outlined"
                                size="small"
                                onChange={handleChange(i)}
                                value={v.value}
                                disabled={props?.disabled}
                            />
                        </div>}
                    </Grid>
                ))
                }
                <div className="SwitchContentChildDiv-2">
                    {labelButton &&
                    <Grid>
                        <Button variant="contained" color="primary" onClick={editLabels}
                                disabled={props?.disabled}>{labelButton}</Button>
                    </Grid>}

                    {showBox &&
                    <Grid>
                        <Button variant="outlined" color="primary" onClick={onCancelHandle}
                                disabled={props?.disabled}>cancel</Button>
                    </Grid>}
                </div>
            </Grid>

            {showBox &&
            <div>
                {props.imageSection}
            </div>
            }
        </div>
    )
}
export default EditLabel;
