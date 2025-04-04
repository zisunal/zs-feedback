<h1>Zs Feedback Settings</h1>
<form class="zs-form" action="javascript:;">
<?php
    $options = get_option( 'zs_fd_settings', [ ] );
?>
    <div class="form-item">
        <label for="enable">Feedback Enabled?</label>
        <label class="rocker rocker-small">
            <input type="checkbox" name="zs-fd-enable" id="zs-fd-enable" value="1" <?php checked( $options['enable'], 1 ); ?> />
            <span class="switch-left">Yes</span>
            <span class="switch-right">No</span>
        </label>
    </div>
    <fieldset>
        <legend>Excluded Users</legend>
        <div class="form-item">
            <ul id="zs-fd-excluded-users-list">
            <?php
            if ( empty( $options['excluded_user_ids'] ) ) {
                echo '<li class="hint">No user excluded</li>';
            }
            foreach( $options['excluded_user_ids'] as $user_id ) {
                $user = get_userdata( $user_id );
                if ( $user ) {
                    echo '<li><a class="dashicons-before dashicons-trash" data-atype="del" data-deltype="excluded_user_ids" data-del="' . $user_id . '"></a>' . esc_html( $user->display_name ) . ' (' . esc_html( $user->user_email ) . ')</li>';
                }
            }
            ?>
            </ul>
            <div class="input">
                <h4>Add to the list</h4>
                <small class="hint">
                    <kbd>Ctrl + Click</kbd> to select multiple options.
                </small>
                <select class="select2" name="zs-fd-excluded-users[]" id="zs-fd-excluded-users" multiple>
                    <?php
                    $users = get_users( );
                    foreach( $users as $user ) {
                        if ( in_array( $user->ID, $options['excluded_user_ids'] ) ) {
                            continue;
                        }
                        echo '<option value="' . esc_attr( $user->ID ) . '">' . esc_html( $user->display_name ) . ' (' . esc_html( $user->user_email ) . ')</option>';
                    }
                    ?>
                </select>
            </div>
        </div>
    </fieldset>
    <fieldset>
        <legend>Excluded User IPs</legend>
        <div class="form-item">
            <ul id="zs-fd-excluded-user-ip-list">
            <?php
            if ( empty( $options['excluded_user_ips'] ) ) {
                echo '<li class="hint">No user ip excluded</li>';
            }
            foreach( $options['excluded_user_ips'] as $user_ip ) {
                echo '<li><a class="dashicons-before dashicons-trash" data-atype="del" data-deltype="excluded_user_ips" data-del="' . $user_ip . '"></a>' . esc_html( $user_ip ) . '</li>';
            }
            ?>
            </ul>
            <div class="input">
                <h4>Add to the list</h4>
                <small class="hint">Hint: 192.168.0.1 or 192.168.0.1-199</small>
                <small class="hint">
                    Hit <kbd>Enter</kbd> to add the ip/ip range to the list.
                </small>
                <input type="text" name="zs-fd-excluded-user-ip" id="zs-fd-excluded-user-ip" value="" placeholder="Enter user ip/ip range" />
            </div>
        </div>
    </fieldset>
    <fieldset>
        <legend>Excluded Browsers/Apps</legend>
        <div class="form-item">
            <ul id="zs-fd-excluded-user-agents-list">
            <?php
            if ( empty( $options['excluded_user_agents'] ) ) {
                echo '<li class="hint">No browser/app excluded</li>';
            }
            foreach( $options['excluded_user_agents'] as $user_agent ) {
                echo '<li><a class="dashicons-before dashicons-trash" data-atype="del" data-deltype="excluded_user_agents" data-del="' . $user_agent . '"></a>' . esc_html( $user_agent ) . '</li>';
            }
            ?>
            </ul>
            <div class="input">
                <h4>Add to the list</h4>
                <small class="hint">
                    <kbd>Ctrl + Click</kbd> to select multiple options.
                </small>
                <select class="select2" name="zs-fd-excluded-user-agents[]" id="zs-fd-excluded-user-agents" multiple>
                    <?php
                    $user_agents = get_option( 'zs_fd_user_agents', [ ] );
                    foreach( $user_agents as $user_agent ) {
                        foreach( $user_agent as $key => $value ) {
                            if ( in_array( $key, $options['excluded_user_agents'] ) ) {
                                continue;
                            }
                            echo '<option value="' . esc_attr( $value ) . '">' . esc_html( str_replace( '_', ' ', ucfirst( $key ) ) ) . '</option>';
                        }
                    }
                    ?>
                </select>
            </div>
        </div>
    </fieldset>
    <button type="button" class="button button-primary" id="settings-sub" style="width:max-content;align-self:end;">Save Settings</button>
    <div class="saver">
        <?php inc_page( 'loader' ) ?>
    </div>
</form>