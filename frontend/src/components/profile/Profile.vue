<template>
  <div class="row my-5">
    <ProfileSidebar />
    <div class="col-md-8">
      <ul class="ss-profile-tabs">
        <li>
          <button
            type="button"
            class="ss-tab-btn"
            :class="{ 'ss-tab-active': activeTab === 'details' }"
            @click="activeTab = 'details'"
          >
            <i class="bi bi-person-lines-fill"></i> Account Details
          </button>
        </li>
        <li>
          <button
            type="button"
            class="ss-tab-btn"
            :class="{ 'ss-tab-active': activeTab === 'password' }"
            @click="activeTab = 'password'"
          >
            <i class="bi bi-shield-lock"></i> Password
          </button>
        </li>
        <li>
          <button
            type="button"
            class="ss-tab-btn ss-tab-danger"
            :class="{ 'ss-tab-active': activeTab === 'delete' }"
            @click="activeTab = 'delete'"
          >
            <i class="bi bi-trash3"></i> Delete Account
          </button>
        </li>
      </ul>
      <UpdateUserInfos v-if="activeTab === 'details'" :updatingProfile="true" />
      <ChangePassword v-if="activeTab === 'password'" />
      <DeleteAccount v-if="activeTab === 'delete'" />
    </div>
  </div>
</template>

<script setup>
    import { ref } from "vue"
    import ProfileSidebar from "./ProfileSidebar.vue"
    import UpdateUserInfos from "./UpdateUserInfos.vue"
    import ChangePassword from "./ChangePassword.vue"
    import DeleteAccount from "./DeleteAccount.vue"

    //controls which profile panel is shown
    const activeTab = ref('details')
</script>

<style scoped>
  .ss-profile-tabs {
    display: flex;
    flex-wrap: wrap;
    gap: 4px;
    list-style: none;
    margin: 0 0 16px;
    padding: 0;
    border-bottom: 1px solid var(--line);
  }
  .ss-tab-btn {
    display: flex;
    align-items: center;
    gap: 8px;
    background: transparent;
    border: none;
    border-bottom: 3px solid transparent;
    padding: 10px 16px;
    margin-bottom: -1px;
    font-family: 'Big Shoulders Display', sans-serif;
    font-weight: 900;
    text-transform: uppercase;
    letter-spacing: .02em;
    font-size: .85rem;
    color: var(--ink-soft, #46564C);
    cursor: pointer;
    transition: color .15s ease, border-color .15s ease;
  }
  .ss-tab-btn:hover { color: var(--ink); }
  .ss-tab-active {
    color: var(--pine, #1B3A2B);
    border-bottom-color: var(--brass, #C9922B);
  }
  .ss-tab-danger.ss-tab-active {
    color: var(--rust, #B54A2C);
    border-bottom-color: var(--rust, #B54A2C);
  }
</style>