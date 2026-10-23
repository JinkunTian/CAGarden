(function ($) {
  'use strict';
  $(function () {
    document.documentElement.classList.add('js');
    var $fields = $('[data-config-field]');
    var baseline = {};
    var saving = false;
    var leaving = false;
    var lastImageButton = null;
    var imageTarget = '';
    var gallerySource = 'photo';
    var allowPhotoSource = true;
    var galleryRequest = null;
    var pendingAction = null;
    var confirmReturnFocus = null;
    var $modal = $('#indexImageBrowser');
    var $endpoints = $('#settingsEndpoints');

    function snapshot() {
      var values = {};
      $fields.each(function () { values[this.name] = $(this).val(); });
      return values;
    }
    baseline = snapshot();

    function dirtyFields() {
      return $fields.filter(function () { return $(this).val() !== baseline[this.name]; });
    }

    function updateState() {
      var $dirty = dirtyFields();
      $('#saveStateText').text($dirty.length ? '有 ' + $dirty.length + ' 项未保存' : '内容已同步');
      $('#saveStateHint').text($dirty.length ? '切换分区会保留修改，记得保存。' : '修改后，保存即可应用新配置。');
      $('#saveStateDot').toggleClass('unsaved', !!$dirty.length);
      $('#saveChanges').prop('disabled', saving || !$dirty.length);
      $('#resetChanges').prop('disabled', saving || !$dirty.length);
      $('.section-link').removeClass('has-changes');
      $dirty.each(function () {
        $('#tab-' + $(this).closest('.settings-panel').attr('id')).addClass('has-changes');
      });
    }

    function notice(message, type) {
      $('#pageNotice').removeAttr('hidden').removeClass('success error').addClass(type || '').text(message);
    }

    function chooseSection(id, moveFocus) {
      var $section = $('.settings-panel').filter(function () { return this.id === id; });
      if (!$section.length) id = 'basic';
      $('.settings-panel').removeClass('active');
      $('#' + id).addClass('active');
      $('.section-link').removeClass('active').attr('aria-selected', 'false').attr('tabindex', '-1');
      var $tab = $('#tab-' + id).addClass('active').attr('aria-selected', 'true').attr('tabindex', '0');
      $('.section-nav').attr('aria-orientation', window.innerWidth <= 800 ? 'horizontal' : 'vertical');
      try {
        window.sessionStorage.setItem('ca-homepage-section', id);
        window.history.replaceState(null, '', '#' + id);
      } catch (ignore) {}
      if (moveFocus) $tab.trigger('focus');
    }

    var initial = window.location.hash.slice(1);
    if (!initial) {
      try { initial = window.sessionStorage.getItem('ca-homepage-section'); } catch (ignore) {}
    }
    chooseSection(initial || 'basic');
    $('.section-link').on('click', function (event) {
      event.preventDefault();
      chooseSection(this.getAttribute('aria-controls'));
    }).on('keydown', function (event) {
      var $tabs = $('.section-link');
      var index = $tabs.index(this);
      if (event.key === 'ArrowDown' || event.key === 'ArrowRight') index = (index + 1) % $tabs.length;
      else if (event.key === 'ArrowUp' || event.key === 'ArrowLeft') index = (index + $tabs.length - 1) % $tabs.length;
      else if (event.key === 'Home') index = 0;
      else if (event.key === 'End') index = $tabs.length - 1;
      else return;
      event.preventDefault();
      chooseSection($tabs.eq(index).attr('aria-controls'), true);
    });
    window.addEventListener('hashchange', function () { chooseSection(window.location.hash.slice(1)); });
    window.addEventListener('resize', function () {
      $('.section-nav').attr('aria-orientation', window.innerWidth <= 800 ? 'horizontal' : 'vertical');
    });

    $fields.on('input change', function () {
      $('#pageNotice').attr('hidden', true);
      updateState();
    });
    window.addEventListener('beforeunload', function (event) {
      if (!leaving && dirtyFields().length) { event.preventDefault(); event.returnValue = ''; }
    });
    document.addEventListener('invalid', function (event) {
      var $section = $(event.target).closest('.settings-panel');
      if ($section.length) chooseSection($section.attr('id'));
      $(event.target).closest('details').prop('open', true);
    }, true);

    $('#indexManageSaveForm').on('submit', function (event) {
      event.preventDefault();
      if (saving || !dirtyFields().length) return;
      var submitted = snapshot();
      saving = true;
      updateState();
      $('#saveChanges').text('正在保存…');
      $.ajax({ url: this.action, type: 'POST', data: $fields.serialize() + '&' + $.param({ csrf_token: $endpoints.attr('data-csrf') }), dataType: 'json', timeout: 15000 })
        .done(function (result) {
          if (result.status == 1) {
            baseline = submitted;
            notice('修改已保存。', 'success');
          } else notice(result.info || '保存失败，请稍后重试。', 'error');
        }).fail(function () {
          notice('未能保存，请检查连接；登录过期时请刷新页面重新登录。', 'error');
        }).always(function () {
          saving = false;
          $('#saveChanges').html('<i class="fa fa-check" aria-hidden="true"></i>保存修改');
          updateState();
        });
    });
    $('#resetChanges').on('click', function () {
      if (!dirtyFields().length) return;
      confirmAction('撤销修改', '撤销当前所有分区的未保存修改？已保存的内容会保留。', this, function () {
        $fields.each(function () { $(this).val(baseline[this.name]); });
        $('.image-url').trigger('input');
        $('#pageNotice').attr('hidden', true);
        updateState();
      });
    });

    function requireSaved() {
      if (saving || dirtyFields().length) {
        notice('请先保存现有修改，再新增或删除项目。');
        $('#saveChanges').trigger('focus');
        return false;
      }
      return true;
    }
    $('.add-form').on('submit', function (event) {
      event.preventDefault();
      if (!requireSaved()) return;
      var form = this;
      var $submit = $(form).find('[type="submit"]');
      $submit.prop('disabled', true).text('正在新增…');
      $.ajax({ url: form.action, type: 'POST', data: $(form).serialize(), dataType: 'json', timeout: 15000 })
        .done(function (result) {
          if (result.status == 1) {
            leaving = true;
            window.location.reload();
          } else notice(result.info || '新增失败，请重试。', 'error');
        }).fail(function () { notice('新增失败，请检查连接后重试。', 'error'); })
        .always(function () { $submit.prop('disabled', false).text('确认新增'); });
    });
    $('.cancel-add').on('click', function () {
      var form = $(this).closest('form')[0];
      form.reset();
      $(form).find('.image-url').trigger('input');
      $(form).find('.upload-status').empty();
      $(this).closest('.add-disclosure').prop('open', false).find('summary').trigger('focus');
    });
    $('.delete-item-form').on('submit', function (event) {
      event.preventDefault();
      if (!requireSaved() || $(this).data('deleting')) return;
      var form = this;
      var $button = $(form).find('[data-delete]');
      confirmAction('删除项目', $button.attr('data-confirm'), $button[0], function () {
        $(form).data('deleting', true);
        $button.prop('disabled', true);
        $.ajax({ url: form.action, type: 'POST', data: $(form).serialize(), dataType: 'json', timeout: 15000 })
          .done(function (result) {
            if (result.status == 1) {
              leaving = true;
              window.location.reload();
            } else notice(result.info || '删除失败，请重试。', 'error');
          }).fail(function () { notice('删除失败，请检查连接后重试。', 'error'); })
          .always(function () { $(form).data('deleting', false); $button.prop('disabled', false).trigger('focus'); });
      }, true);
    });

    function confirmAction(title, message, source, action, destructive) {
      pendingAction = action;
      confirmReturnFocus = source;
      $('#confirmTitle').text(title);
      $('#confirmMessage').text(message);
      $('#confirmAccept').text(destructive ? '确认删除' : '确认撤销').toggleClass('button-danger', !!destructive);
      $('#settingsConfirm').removeAttr('hidden');
      $('body').addClass('modal-open');
      $('#confirmCancel').trigger('focus');
    }
    function closeConfirm() {
      $('#settingsConfirm').attr('hidden', true);
      $('body').removeClass('modal-open');
      if (confirmReturnFocus) $(confirmReturnFocus).trigger('focus');
      pendingAction = null;
    }
    $('#confirmCancel').on('click', closeConfirm);
    $('#confirmAccept').on('click', function () {
      var action = pendingAction;
      closeConfirm();
      if (action) action();
    });
    $('#settingsConfirm').on('click', function (event) { if (event.target === this) closeConfirm(); })
      .on('keydown', function (event) {
        if (event.key === 'Escape') { event.preventDefault(); closeConfirm(); }
        if (event.key === 'Tab') {
          event.preventDefault();
          if (document.activeElement.id === 'confirmCancel') $('#confirmAccept').trigger('focus');
          else $('#confirmCancel').trigger('focus');
        }
      });

    function refreshImage(input) {
      var $preview = $('#' + input.id.replace(/-url$/, '-preview'));
      var url = $(input).val().trim();
      $preview.css('display', url ? '' : 'none');
      if (url) $preview.attr('src', url);
      else $preview.removeAttr('src');
      if (input.id === 'brand-icon-url') $('#headerBrandIcon').attr('src', url);
    }
    $('#restoreBrandIcon').on('click', function () {
      $('#brand-icon-url').val('/Public/images/ca-admin-hammer.webp').trigger('input');
      $('.brand-editor .upload-status').text('默认图标已选用，保存后生效。');
    });
    $('.image-url').on('input change', function () { refreshImage(this); });
    $('.image-preview img').on('error', function () {
      $(this).hide().siblings('.image-placeholder').find('span').text('图片暂不可用');
    }).on('load', function () { $(this).show(); });
    $('.image-preview img').each(function () {
      if (this.complete && this.getAttribute('src') && !this.naturalWidth) $(this).trigger('error');
    });

    function compressBrandFile(file) {
      return new Promise(function (resolve, reject) {
        if (file.size > 4194304) { reject(new Error('请选择 4MB 以内的图片。')); return; }
        if (!/^image\/(jpeg|png|webp)$/.test(file.type)) { reject(new Error('请选择 JPG、PNG 或 WebP 图片。')); return; }
        var url = URL.createObjectURL(file);
        var image = new Image();
        image.onerror = function () { URL.revokeObjectURL(url); reject(new Error('图片无法读取，请换一张图片。')); };
        image.onload = function () {
          if (image.naturalWidth * image.naturalHeight > 8388608) {
            URL.revokeObjectURL(url); reject(new Error('请先将图片缩小到 800 万像素以内。')); return;
          }
          var scale = Math.min(1, 512 / image.naturalWidth, 512 / image.naturalHeight);
          var canvas = document.createElement('canvas');
          canvas.width = Math.max(1, Math.round(image.naturalWidth * scale));
          canvas.height = Math.max(1, Math.round(image.naturalHeight * scale));
          canvas.getContext('2d').drawImage(image, 0, 0, canvas.width, canvas.height);
          URL.revokeObjectURL(url);
          canvas.toBlob(function (blob) {
            if (!blob) { reject(new Error('图片压缩失败，请重试。')); return; }
            resolve(new File([blob], 'brand-icon.webp', { type: 'image/webp' }));
          }, 'image/webp', .88);
        };
        image.src = url;
      });
    }
    $('.image-file').on('change', function () {
      var input = this;
      var file = input.files && input.files[0];
      if (!file) return;
      var isBrand = input.getAttribute('data-brand') === 'true';
      var $scope = $(input).closest('.item-card, .new-image-grid, .brand-editor');
      var $status = $scope.find('.upload-status').first();
      $status.text(isBrand ? '正在压缩图标…' : '正在上传图片…');
      $(input).prop('disabled', true);
      (isBrand ? compressBrandFile(file) : Promise.resolve(file)).then(function (preparedFile) {
        var body = new FormData();
        body.append('image', preparedFile);
        body.append('csrf_token', $endpoints.attr('data-csrf'));
        $status.text('正在上传图片…');
        $.ajax({ url: $endpoints.attr(isBrand ? 'data-brand' : 'data-upload'), type: 'POST', data: body,
        processData: false, contentType: false, dataType: 'json', timeout: 60000 })
        .done(function (result) {
          if (result.status == 1 && result.url) {
            $(input.getAttribute('data-url-target')).val(result.url).trigger('input');
            $status.text(isBrand ? '已压缩至 ' + (result.bytes / 1024).toFixed(1) + ' KB，保存后使用新图标。' : '已上传，保存后应用到首页。');
          } else $status.text(result.info || '上传失败，请重试。');
        }).fail(function () { $status.text('上传失败，请检查连接后重试。'); })
        .always(function () { $(input).prop('disabled', false).val(''); });
      }).catch(function (error) {
        $status.text(error.message || '图片处理失败，请重试。');
        $(input).prop('disabled', false).val('');
      });
    });

    function closeGallery() {
      if (galleryRequest) galleryRequest.abort();
      $modal.attr('hidden', true);
      $('body').removeClass('modal-open');
      if (lastImageButton) $(lastImageButton).trigger('focus');
    }
    function setSource(source) {
      gallerySource = !allowPhotoSource || source === 'main' ? 'main' : 'photo';
      $('[data-source]').each(function () {
        var active = this.getAttribute('data-source') === gallerySource;
        var blocked = !allowPhotoSource && this.getAttribute('data-source') === 'photo';
        $(this).prop('disabled', blocked).attr('aria-disabled', blocked ? 'true' : 'false')
          .toggleClass('active', active).attr('aria-pressed', active ? 'true' : 'false');
      });
    }
    $('.image-browse').on('click', function () {
      lastImageButton = this;
      imageTarget = this.getAttribute('data-url-target');
      allowPhotoSource = this.getAttribute('data-allow-photo') !== 'false';
      $('#galleryTitle').text(allowPhotoSource ? '选择展示图片' : '选择主站 Logo 图片');
      setSource(this.getAttribute('data-image-source'));
      $('#galleryKeyword').val('');
      $modal.removeAttr('hidden');
      $('body').addClass('modal-open');
      $('#galleryKeyword').trigger('focus');
      loadGallery(1);
    });
    $('#galleryClose').on('click', closeGallery);
    $modal.on('click', function (event) { if (event.target === this) closeGallery(); });
    $modal.on('keydown', function (event) {
      if (event.key === 'Escape') { event.preventDefault(); closeGallery(); }
      if (event.key !== 'Tab') return;
      var $focusable = $modal.find('button, input, [tabindex="0"]').filter(':visible').not(':disabled');
      var first = $focusable[0];
      var last = $focusable[$focusable.length - 1];
      if (event.shiftKey && document.activeElement === first) { event.preventDefault(); $(last).trigger('focus'); }
      else if (!event.shiftKey && document.activeElement === last) { event.preventDefault(); $(first).trigger('focus'); }
    });
    $('[data-source]').on('click', function () { setSource(this.getAttribute('data-source')); loadGallery(1); });
    $('#gallerySearch').on('click', function () { loadGallery(1); });
    $('#galleryKeyword').on('keydown', function (event) {
      if (event.key === 'Enter') { event.preventDefault(); loadGallery(1); }
    });

    function galleryMessage(text) {
      $('#galleryGrid').empty().append($('<div class="empty-state"></div>').text(text));
    }
    function loadGallery(page) {
      if (galleryRequest) galleryRequest.abort();
      galleryMessage('正在加载图片…');
      $('#galleryGrid').attr('aria-busy', 'true');
      $('#galleryPages').empty();
      $('#gallerySummary').text('正在加载图片…');
      galleryRequest = $.getJSON($endpoints.attr('data-' + gallerySource), { page: page, keyword: $('#galleryKeyword').val() })
        .done(function (result) {
          if (result.status != 1) {
            galleryMessage(result.info || '图片暂不可用，请稍后再试。');
            $('#gallerySummary').text(allowPhotoSource ? '可切换到另一种图片来源。' : '兄弟社团 Logo 仅支持主站图片，也可以直接上传。');
            return;
          }
          renderGallery(result);
        }).fail(function (xhr, status) {
          if (status === 'abort') return;
          galleryMessage('图片加载失败，请检查连接后重试。');
          $('#gallerySummary').text('加载失败');
        }).always(function () { $('#galleryGrid').attr('aria-busy', 'false'); });
    }
    function renderGallery(result) {
      var $grid = $('#galleryGrid').empty();
      $('#gallerySummary').text('共 ' + result.total + ' 张图片 · 第 ' + result.page + ' / ' + result.pages + ' 页');
      if (!result.items || !result.items.length) galleryMessage('没有找到图片，试试其他关键词。');
      $.each(result.items || [], function (index, item) {
        var $item = $('<button type="button" class="gallery-item"></button>').attr('title', item.name || '展示图片');
        $('<img loading="lazy" />').attr({ src: item.url, alt: item.name || '展示图片' }).appendTo($item);
        $('<span></span>').text(item.name || '展示图片').appendTo($item);
        $item.on('click', function () {
          $(imageTarget).val(item.url).trigger('input');
          if (item.album_url && /album/.test(imageTarget)) {
            $(imageTarget.replace(/-url$/, '-link')).val(item.album_url).trigger('input');
          }
          closeGallery();
        });
        $grid.append($item);
      });
      var $pages = $('#galleryPages').empty();
      if (result.pages <= 1) return;
      function pageButton(page, label, current) {
        $('<button type="button"></button>').text(label).toggleClass('active', !!current)
          .attr('aria-label', '第 ' + page + ' 页').attr('aria-current', current ? 'page' : null)
          .on('click', function () { loadGallery(page); }).appendTo($pages);
      }
      if (result.page > 1) pageButton(result.page - 1, '‹');
      var start = Math.max(1, Math.min(result.page - 2, result.pages - 4));
      for (var p = start; p <= Math.min(result.pages, start + 4); p++) pageButton(p, p, p == result.page);
      if (result.page < result.pages) pageButton(result.page + 1, '›');
    }
    updateState();
  });
})(jQuery);
