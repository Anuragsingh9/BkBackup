import {ColorRGBA} from './index.js';

/**
 * @type {Object}
 * @property {ColorRGBA} background_color Background Color of application
 * @property {ColorRGBA} separation_line_color Separation line color for section
 * @property {ColorRGBA} text_color Text color on application
 * @property {ColorRGBA} event_color_1 Event Main Color 1
 * @property {ColorRGBA} event_color_2 Event Main Color 2
 * @property {ColorRGBA} event_color_3 Event Main Color 3
 * @property {ColorRGBA} tag_color Color Value for tags
 * @property {ColorRGBA} join_bg_color Join Button color on QSS
 * @property {ColorRGBA} join_text_color Text Color on Join Button
 * @property {ColorRGBA} professional_tag_color User badge professional tag color
 * @property {ColorRGBA} personal_tag_color User Badge Personal tag color
 * @property {Number} has_custom_background To indicate if application using own graphics settings or not
 * @property {Number} customized_texture To indicate if  the application having texture settings customised or not
 * @property {Number} texture_square_corner To show the corner or boxes rounded or sharp
 * @property {Number} texture_remove_frame To remove the background frame of texture
 * @property {Number} texture_remove_shadow To remove the shadow of boxes
 * @property {Number} customized_colors To indicate if color customization is applied or not
 * @property {Number} unselected_spaces_square To show the unselected spaces as square boxes
 * @property {Number} selected_spaces_square To show the current or selected space square or rounded
 * @property {Number} label_customization To apply the customization of label customization
 * @property {String|Number|File} kct_graphics_logo To logo value for header
 * @property {ColorRGBA} kct_graphics_color1 Main graphics color 1 value
 * @property {ColorRGBA} kct_graphics_color2 Main graphics color 2 value
 * @property {String} video_url Url Value for Video to show on QSS
 * @property {Number} sh_background To indicate if space host section is customised or not
 * @property {Number} conv_background To indicate if  conversation section is customised or not
 * @property {Number} badge_background To indicate if badge section is customised or not
 * @property {Number} space_background To indicate if space background section is customised or not
 * @property {Number} user_grid_background To indicate if user grid section is customised or not
 * @property {Number} user_grid_pagination_color To indicate if user grid pagination buttons are customised or not
 * @property {Number} tags_text_color To indicate if Tags Text is customised or not
 * @property {Number} content_background To indicate if content player is customised or not
 * @property {Number} button_customized To indicate if application buttons are customised or not
 * @property {Number} sh_customized To indicate if space host section is customised or not
 * @property {Number} sh_hide_on_off To indicate the space host section visibility
 * @property {Number} conv_customization To show the conversation section customised
 * @property {Number} badge_customization To show the badge section customised
 * @property {Number} space_customization To show the space section customised
 * @property {Number} extends_color_user_guide To show the user guide section custom colored
 * @property {Number} user_grid_customization  To show the user grid section customised
 * @property {Number} tags_customization  To show the tags section customised
 * @property {Number} content_customized  To show the content section customised
 * @property {Number} label_customized  To show the labels customised
 * @property {Number} general_setting To apply the general settings customized so user will follow the customised setting
 * @property {Number} invite_attendee  To show the Invite user section customised
 * @property {Number} video_explainer  To show the video section
 * @property {String} header_line_1 Header line 1 value
 * @property {String} header_line_2 Header line 2 value
 * @property {String} event_image Event default image value when no content is there
 * @property {String} business_team_icon Business Team Icon Url
 * @property {String} business_team_altImage Business Team Icon alternative Icon Url
 * @property {String} vip_icon Vip Icon URL
 * @property {String} vip_altImage Vip Icon Alternative URL
 * @property {String} moderator_icon Moderator Icon URL
 * @property {String} expert_icon Expert Icon URL
 * @property {String} expert_altImage Expert Icon Alternative URL
 * @property {String} video_explainer_alternative_image Video Explainer or Grid Image Alternative Image URL
 * @property {Number} bottom_bg_is_colored To show footer of application customised
 * @property {Number} video_explainer_enabled Visibility of Video Explainer
 * @property {Number} display_on_reg To display the video explainer on registration page
 * @property {Number} display_on_live To display the video explainer on dashboard page
 */
const GraphicsData = {
    background_color: {
        r: 255,
        g: 255,
        b: 255,
        a: 1
    },
    separation_line_color: {
        r: 231,
        g: 231,
        b: 231,
        a: 1
    },
    text_color: {
        r: 59,
        g: 59,
        b: 59,
        a: 1
    },
    event_color_1: {
        r: 144,
        g: 19,
        b: 254,
        a: 1
    },
    event_color_2: {
        r: 80,
        g: 227,
        b: 194,
        a: 1
    },
    event_color_3: {
        r: 144,
        g: 19,
        b: 254,
        a: 1
    },
    tag_color: {
        r: 5,
        g: 137,
        b: 184,
        a: 1
    },
    join_bg_color: {
        r: 65,
        g: 144,
        b: 182,
        a: 1
    },
    join_text_color: {
        r: 255,
        g: 255,
        b: 255,
        a: 1
    },
    professional_tag_color: {
        r: 109,
        g: 53,
        b: 173,
        a: 1
    },
    personal_tag_color: {
        r: 53,
        g: 173,
        b: 129,
        a: 1
    },
    has_custom_background: 0,
    customized_texture: 0,
    texture_square_corner: 0,
    texture_remove_frame: 0,
    texture_remove_shadow: 0,
    customized_colors: 1,
    unselected_spaces_square: 0,
    selected_spaces_square: 0,
    label_customization: 0,
    kct_graphics_logo: "https://s3.eu-west-2.amazonaws.com/kct-dev/general/group_logo/default.png",
    kct_graphics_color1: {
        r: 144,
        g: 19,
        b: 254,
        a: 1
    },
    kct_graphics_color2: {
        r: 80,
        g: 227,
        b: 194,
        a: 1
    },
    video_url: "https://www.youtube.com/watch?v=VnyitUU4DUY",
    sh_background: {
        r: 59,
        g: 59,
        b: 59,
        a: 1
    },
    conv_background: {
        r: 59,
        g: 59,
        b: 59,
        a: 1
    },
    badge_background: {
        r: 255,
        g: 255,
        b: 255,
        a: 1
    },
    space_background: {
       r: 59,
        g: 59,
        b: 59,
        a: 0.75
    },
    user_grid_background: {
        r: 59,
        g: 59,
        b: 59,
        a: 1
    },
    user_grid_pagination_color: {
        r: 5,
        g: 137,
        b: 184,
        a: 1
    },
    tags_text_color: {
        r: 255,
        g: 255,
        b: 255,
        a: 1
    },
    content_background: {
        r: 59,
        g: 59,
        b: 59,
        a: 1
    },
    button_customized: 1,
    sh_customized: 1,
    sh_hide_on_off: 1,
    conv_customization: 0,
    badge_customization: 0,
    space_customization: 0,
    extends_color_user_guide: 0,
    user_grid_customization: 0,
    tags_customization: 0,
    content_customized: 1,
    label_customized: 1,
    general_setting: 1,
    invite_attendee: 0,
    video_explainer: 1,
    header_line_1: null,
    header_line_2: null,
    event_image: "https://s3.eu-west-2.amazonaws.com/kct-dev/general/event_image/default.jpg",
    business_team_icon: null,
    business_team_altImage: null,
    vip_icon: null,
    vip_altImage: null,
    moderator_icon: null,
    expert_icon: null,
    expert_altImage: null,
    video_explainer_alternative_image: "https://s3.eu-west-2.amazonaws.com/kct-dev/assets/default-video-explainer-alt.png",
    bottom_bg_is_colored: 0,
    video_explainer_enabled: 1,
    display_on_reg: 1,
    display_on_live: 1
};

export default GraphicsData;