import React from 'react'
import "./NoDataDiv.css"

const NoDataDiv = (props) => {
    return (
        <div className='noData_divWrap'>
            <p className='no_data_div'>{props.showText}</p>
        </div>
    )
}

export default NoDataDiv