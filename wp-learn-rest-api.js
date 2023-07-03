/**
 * This code uses the Backbone.js that ships with WordPress
 */

/**
 * Fetch posts
 */
const fetchPosts = () => {
  const allPosts = new wp.api.collections.Posts();
  allPosts.fetch({ data: { _fields: 'id, title' } }).done(function (posts) {
    const textarea = document.getElementById('wp-learn-posts');
    textarea.value = '';
    posts.forEach(function (post) {
      textarea.value += `${post.title.rendered}—${post.id}\n`;
    });
  });
};

const loadPostsByRestButton = document.getElementById('wp-learn-rest-api-button');

if (loadPostsByRestButton) {
  loadPostsByRestButton.addEventListener('click', fetchPosts);
}

/**
 * Clear the text area button
 */
const clearPostsButton = document.getElementById('wp-learn-clear-posts');
if (typeof clearPostsButton != 'undefined' && clearPostsButton != null) {
  clearPostsButton.addEventListener('click', function () {
    const textarea = document.getElementById('wp-learn-posts');
    textarea.value = '';
  });
}

/**
 * Create new post
 */
function submitPost() {
  const title = document.getElementById('wp-learn-post-title').value;
  const content = document.getElementById('wp-learn-post-content').value;
  const url = document.getElementById('wp-learn-post-url').value;
  const post = new wp.api.models.Post({
    title: title,
    content: content,
    status: 'publish',
    meta: {
      url: url
    }
  });
  post.save().done(function (post) {
    alert('Post saved!');
  });
}

const submitPostButton = document.getElementById('wp-learn-submit-post');
if (submitPostButton) {
  submitPostButton.addEventListener('click', submitPost);
}

/**
 * Update a post
 */
function updatePost() {
  const id = document.getElementById('wp-learn-update-post-id').value;
  const title = document.getElementById('wp-learn-update-post-title').value;
  const content = document.getElementById('wp-learn-update-post-content').value;
  const post = new wp.api.models.Post({
    id: id,
    title: title,
    content: content
  });
  post.save().done(function (post) {
    alert('Post Updated!');
  });
}

const updatePostButton = document.getElementById('wp-learn-update-post');
if (updatePostButton) {
  updatePostButton.addEventListener('click', updatePost);
}

/**
 * Delete a post
 */
function deletePost() {
  const id = document.getElementById('wp-learn-post-id').value;
  const post = new wp.api.models.Post({ id: id });
  post.destroy().done(function (post) {
    alert('Post deleted!');
  });
}

const deletePostButton = document.getElementById('wp-learn-delete-post');
if (typeof deletePostButton != 'undefined' && deletePostButton != null) {
  deletePostButton.addEventListener('click', deletePost);
}
