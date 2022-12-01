import React from 'react'
import "./CustomContainer.css"

const CustomContainer = (props) => {
    const {children} = props;
  return (
    <div className={`CustomContainer ${props?.className}`}>
        {children}
    </div>
  )
}

export default CustomContainer