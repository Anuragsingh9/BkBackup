import React from 'react';
import Image from './Image';

const imageUrl = "https://png.pngtree.com/png-clipart/20190924/original/pngtree-user-vector-avatar-png-image_4830521.jpg";

/**
 * @deprecated
 * @returns {JSX.Element}
 * @class
 * @component
 * @constructor
 */
const BadgeImage = () => {


    return (
        <div>
            <Image image={imageUrl} />
        </div>
    )
}


export default BadgeImage;