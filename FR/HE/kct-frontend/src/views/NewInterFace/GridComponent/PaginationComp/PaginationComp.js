import React from 'react';
import './PaginationComp.css';

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This component is for rendering/displaying pagination buttons and it also prepares data for handling
 * pagination when pagination buttons are clicked on users grid component.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Number} current_page Current page number
 * @param {Number} total_page Count of total pages on basis of event list
 * @param {Function} onPageChange To handle when the user change the page
 * @returns {JSX.Element}
 * @class
 * @component
 * @constructor
 */
const PaginationComp = ({current_page, total_page, onPageChange}) => {

    const pageArray = Array(total_page).fill('');
    return (
        <nav aria-label="Page navigation" className="PaginationDiv">
            <ul className="pagination">
                {pageArray.map((item, key) => {
                    return (
                        <li className={`page-item ${key + 1 == current_page ? 'selected' : ''}`} onClick={() => {
                            onPageChange(key + 1)
                        }}></li>
                    )
                })}
            </ul>
        </nav>
    )
}


export default PaginationComp;