import React, {useState} from 'react'
import {AdapterMoment} from "@mui/x-date-pickers/AdapterMoment";
import {LocalizationProvider} from '@mui/x-date-pickers-pro';
import {Box, TextField} from "@mui/material";
import {DateRangePicker} from "@mui/x-date-pickers-pro/DateRangePicker";

const DateRangeSelector = (props) => {
    const [value, setValue] = useState([null, null]);

    const handleDateChange = (newValue) => { //ToDo "handle" + method namwe
        setValue(newValue);
    }

    return (
        <div>
            <React.Fragment>
                <LocalizationProvider
                    dateAdapter={AdapterMoment}
                    localeText={{start: '', end: ''}}
                >
                    <DateRangePicker
                        value={value || [null, null]}
                        onChange={handleDateChange}
                        renderInput={(startProps, endProps) => (
                            <React.Fragment>
                                <TextField
                                    className="customDateRangePicker"
                                    {...startProps}
                                />
                                <Box sx={{mx: 2}}> to </Box>
                                <TextField
                                    className="customDateRangePicker"
                                    {...endProps}
                                />
                            </React.Fragment>
                        )}
                        disablePast
                        variant="filled"
                        id={props.id}
                    />
                </LocalizationProvider>
            </React.Fragment>
        </div>
    )
}

export default DateRangeSelector