import ColorRGBA from "../../Model/ColorRGBA";

const EventGraphics = {
    background_color: ColorRGBA,
    separation_line_color: ColorRGBA,
    text_color: ColorRGBA,
    event_color_1: ColorRGBA,
    event_color_2: ColorRGBA,
    event_color_3: ColorRGBA,
    tag_color: ColorRGBA,
    join_bg_color: ColorRGBA,
    join_text_color: ColorRGBA,
    professional_tag_color: ColorRGBA,
    personal_tag_color: ColorRGBA,
    kct_graphics_color1: ColorRGBA,
    kct_graphics_color2: ColorRGBA,
    sh_background: ColorRGBA,
    conv_background: ColorRGBA,
    badge_background: ColorRGBA,
    space_background: ColorRGBA,
    user_grid_background: ColorRGBA,
    user_grid_pagination_color: ColorRGBA,
    tags_text_color: ColorRGBA,
    content_background: ColorRGBA,

    has_custom_background: 0,
    customized_texture: 0,
    texture_square_corner: 0,
    texture_remove_frame: 0,
    texture_remove_shadow: 0,
    customized_colors: 0,
    unselected_spaces_square: 0,
    selected_spaces_square: 0,
    label_customization: 0,
    button_customized: 0,
    sh_customized: 0,
    sh_hide_on_off: 0,
    conv_customization: 0,
    badge_customization: 0,
    space_customization: 0,
    extends_color_user_guide: 0,
    user_grid_customization: 0,
    tags_customization: 0,
    content_customized: 0,
    label_customized: 0,
    general_setting: 0,
    invite_attendee: 0,
    video_explainer: 1,
    group_has_own_customization: 0,

    header_line_1: null,
    header_line_2: null,

    business_team_icon: null,
    vip_icon: null,
    moderator_icon: null,
    expert_icon: null,
    business_team_altImage: null,
    vip_altImage: null,
    expert_altImage: null,

    event_image: "https://s3.eu-west-2.amazonaws.com/kct-dev/general/event_image/default.jpg",
    video_explainer_alternative_image: "https://s3.eu-west-2.amazonaws.com/kct-dev/assets/default-video-explainer-alt.png",
    kct_graphics_logo: "https://s3.eu-west-2.amazonaws.com/kct-dev/first.kct.local/groups/logo/b53a17e0-4ed0-11ed-b890-38f3ab76fa54.png",
    video_url: "https://www.youtube.com/watch?v=VnyitUU4DUY",

};


/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This method ensure that all the required key for the event graphics is present in return
 * so in redux there will be always the keys which are required to run the application
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param graphics
 * @returns {*}
 */
const validateGraphicsObject = (graphics) => {
    
    return graphics;
}

let eventGraphicReduxHelper = {
    validateGraphicsObject: validateGraphicsObject,
}

export default eventGraphicReduxHelper;