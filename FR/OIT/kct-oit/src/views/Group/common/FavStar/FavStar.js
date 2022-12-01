import React, {useEffect, useState} from 'react';
import {Radio, Button} from '@mui/material';
import StarOutlineIcon from '@mui/icons-material/StarOutline';
import StarIcon from '@mui/icons-material/Star';
import "./FavStar.css";

/**
 * @class
 * @component
 *
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This component is developed to add a specific group as favourite group from the group list page.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Object} props Object that contains favourite value for a group
 * @param {Boolean} props.fav Favourite value
 * @returns {JSX.Element}
 */
const FavStar = (props) => {
    const [fav, setfav] = useState(false);


    useEffect(() => {
        if (props) {
            setfav(props.fav)
        }
    }, [])

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method will manage the favourite value and update it in a state.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     */
    const handleClick = () => {
        setfav(!fav);
        props.setFav(!fav)
    }

    return (
        <span className='Fav_Star' onClick={handleClick}>
            {fav == true ? <StarIcon /> : <StarOutlineIcon />}
        </span>
    );
}
export default FavStar;
