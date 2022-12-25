

  
    <p id="gotop"><a href="#prelude"><?php echo __('Page top'); ?></a></p>
  
  
    <footer class="footer" id="footer" role="contentinfo">
      
        <?php if(publicWidgets::ifWidgetsHandler('custom','')) : ?>
          <div class="widgets footer__widgets" id="blogcustom">
            
              <h2 class="blogcustom__title"><?php echo __('Blog info'); ?></h2>
            
            
              <?php publicWidgets::widgetsHandler('custom',''); ?>
            
          </div> 
        <?php endif; ?>
      
      
        <?php if (dcCore::app()->hasBehavior('publicInsideFooter')) { dcCore::app()->callBehavior('publicInsideFooter',dcCore::app(),dcCore::app()->ctx);} ?>
      
      
        <p><?php printf(__("Powered by %s"),"<a href=\"https://dotclear.org/\">Dotclear</a>"); ?></p>
      
    </footer>
  
  
    <?php if (dcCore::app()->hasBehavior('publicFooterContent')) { dcCore::app()->callBehavior('publicFooterContent',dcCore::app(),dcCore::app()->ctx);} ?>
  
  
    <?php try { echo dcCore::app()->tpl->getData('user_footer.html'); } catch (Exception $e) {} ?>

  


